<?php
class SocieteController extends BaseController {

    public function liste()
    {
        // Vérification : Seul l'admin peut accéder
        if ($_SESSION['role'] !== 'admin') {
            Logger::warning('Tentative accès sociétés non autorisée', [
                'utilisateur_id' => $_SESSION['utilisateur_id'],
                'role' => $_SESSION['role']
            ]);
            setFlash('danger', 'Accès réservé aux administrateurs');
            redirect('/utilisateur/dashboard');
        }

        $con = getDatabaseConnection();

        // AJOUT d'une société
        if (isset($_POST['btn-ajouter'])) {
            $validator = $this->validate($_POST, [
                'sai_nom_societe' => 'required|min:3|max:150'
            ]);

            if ($validator) {
                saveOldInput();
                setFlash('danger', $validator->firstError());
            } else {
                try {
                    $nom = trim($_POST['sai_nom_societe']);
                    $logo = null;

                    // Upload du logo
                    if (!empty($_FILES['sai_logo']['name'])) {
                        try {
                            $logo = uploadFile($_FILES['sai_logo'], 'uploads/logos',
                                ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
                        } catch (Exception $e) {
                            Logger::error('Erreur upload logo société', ['error' => $e->getMessage()]);
                            setFlash('warning', 'Société ajoutée mais erreur lors de l\'upload du logo');
                        }
                    }

                    $stmt = $con->prepare("INSERT INTO societes (nom_societe, logo_societe) VALUES (?, ?)");
                    $stmt->execute([$nom, $logo]);

                    $newId = $con->lastInsertId();

                    clearOldInput();
                    Logger::info('Société créée', [
                        'id_societe' => $newId,
                        'nom' => $nom,
                        'par' => $_SESSION['utilisateur_id']
                    ]);

                    setFlash('success', 'Société ajoutée avec succès');
                } catch (Exception $e) {
                    Logger::logException($e);
                    setFlash('danger', isDebug() ? 'Erreur: ' . $e->getMessage() : 'Erreur lors de l\'ajout');
                }
            }
            redirect('/societe/liste');
        }

        // MODIFICATION d'une société
        if (isset($_POST['btn-modifier'])) {
            $validator = $this->validate($_POST, [
                'id_societe' => 'required|integer',
                'sai_nom_societe' => 'required|min:3|max:150'
            ]);

            if ($validator) {
                setFlash('danger', $validator->firstError());
            } else {
                try {
                    $id = (int)$_POST['id_societe'];
                    $nom = trim($_POST['sai_nom_societe']);
                    $logo = $_POST['logo_existant'] ?? null;

                    // Upload du nouveau logo
                    if (!empty($_FILES['sai_logo']['name'])) {
                        try {
                            // Supprimer l'ancien logo
                            if ($logo) {
                                deleteFile($logo);
                            }
                            $logo = uploadFile($_FILES['sai_logo'], 'uploads/logos',
                                ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
                        } catch (Exception $e) {
                            Logger::error('Erreur upload logo société', ['error' => $e->getMessage()]);
                        }
                    }

                    $stmt = $con->prepare("UPDATE societes SET nom_societe = ?, logo_societe = ? WHERE id_societe = ?");
                    $stmt->execute([$nom, $logo, $id]);

                    Logger::info('Société modifiée', [
                        'id_societe' => $id,
                        'nom' => $nom,
                        'par' => $_SESSION['utilisateur_id']
                    ]);

                    setFlash('success', 'Société modifiée avec succès');
                } catch (Exception $e) {
                    Logger::logException($e);
                    setFlash('danger', isDebug() ? 'Erreur: ' . $e->getMessage() : 'Erreur lors de la modification');
                }
            }
            redirect('/societe/liste');
        }

        // SUPPRESSION d'une société
        if (isset($_POST['btn-supprimer'])) {
            $id = (int)($_POST['id_societe'] ?? 0);
            if ($id > 0) {
                try {
                    // Vérifier s'il y a des données liées
                    $check = $con->prepare("SELECT COUNT(*) as count FROM boutiques WHERE id_societe = ?");
                    $check->execute([$id]);
                    $boutiquesCount = $check->fetch()['count'];

                    if ($boutiquesCount > 0) {
                        setFlash('danger', "Impossible de supprimer : cette société a $boutiquesCount boutique(s) associée(s)");
                    } else {
                        $stmt = $con->prepare("DELETE FROM societes WHERE id_societe = ?");
                        $stmt->execute([$id]);

                        Logger::warning('Société supprimée', [
                            'id_societe' => $id,
                            'par' => $_SESSION['utilisateur_id']
                        ]);

                        setFlash('success', 'Société supprimée');
                    }
                } catch (Exception $e) {
                    Logger::logException($e);
                    setFlash('danger', 'Impossible de supprimer cette société');
                }
            }
            redirect('/societe/liste');
        }

        // LISTE des sociétés avec statistiques
        try {
            $query = "
                SELECT
                    s.*,
                    COUNT(DISTINCT b.code_boutique) as nb_boutiques,
                    COUNT(DISTINCT u.utilisateur_id) as nb_utilisateurs
                FROM societes s
                LEFT JOIN boutiques b ON s.id_societe = b.id_societe
                LEFT JOIN utilisateurs u ON s.id_societe = u.id_societe
                GROUP BY s.id_societe
                ORDER BY s.nom_societe
            ";
            $result = $con->query($query)->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            Logger::logException($e);
            $result = [];
            setFlash('warning', 'Erreur lors du chargement des sociétés');
        }

        $this->render('societe/liste', ['result' => $result]);
    }

    /**
     * Paramètres FNE de la société (informations fiscales et bancaires)
     * Accessible par : admin, manager
     */
    public function parametres()
    {
        // Vérification : Admin ou Manager
        if (!in_array($_SESSION['role'], ['admin', 'manager'])) {
            setFlash('danger', 'Accès réservé aux administrateurs et managers');
            redirect('/utilisateur/dashboard');
        }

        $con = getDatabaseConnection();

        // Récupérer les informations de la société
        $stmt = $con->prepare("SELECT * FROM societes WHERE id_societe = ?");
        $stmt->execute([getSocieteActive()]);
        $societe = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$societe) {
            setFlash('error', 'Société introuvable');
            redirect('/utilisateur/dashboard');
        }

        // Mise à jour des paramètres
        if ($this->isPostRequest() && isset($_POST['btn-update-societe'])) {
            if (!CSRF::verifyRequest()) {
                setFlash('error', 'Token CSRF invalide');
                redirect('/societe/parametres');
            }

            try {
                $stmt = $con->prepare("
                    UPDATE societes SET
                        nom_societe = ?,
                        compte_contribuable = ?,
                        numero_rcc = ?,
                        regime_imposition = ?,
                        service_impots = ?,
                        adresse_complete = ?,
                        nom_banque = ?,
                        code_guichet = ?,
                        numero_compte = ?,
                        cle_rib = ?,
                        code_swift = ?
                    WHERE id_societe = ?
                ");

                $stmt->execute([
                    $_POST['nom_societe'] ?? '',
                    $_POST['compte_contribuable'] ?? '',
                    $_POST['numero_rcc'] ?? '',
                    $_POST['regime_imposition'] ?? 'Réel Normal',
                    $_POST['service_impots'] ?? '',
                    $_POST['adresse_complete'] ?? '',
                    $_POST['nom_banque'] ?? '',
                    $_POST['code_guichet'] ?? '',
                    $_POST['numero_compte'] ?? '',
                    $_POST['cle_rib'] ?? '',
                    $_POST['code_swift'] ?? '',
                    getSocieteActive()
                ]);

                Logger::info('Paramètres société mis à jour', [
                    'id_societe' => getSocieteActive(),
                    'par' => $_SESSION['utilisateur_id']
                ]);

                setFlash('success', 'Paramètres de la société mis à jour avec succès !');
                redirect('/societe/parametres');
            } catch (Exception $e) {
                Logger::error('Erreur mise à jour société', ['error' => $e->getMessage()]);
                setFlash('error', 'Erreur lors de la mise à jour');
                redirect('/societe/parametres');
            }
        }

        $this->render('societe/parametres', [
            'societe' => $societe
        ]);
    }

    /**
     * Page de configuration API DGI pour le client
     * Accessible par : admin, manager
     */
    public function configuration_dgi()
    {
        // Vérification : Admin ou Manager
        if (!in_array($_SESSION['role'], ['admin', 'manager'])) {
            setFlash('danger', 'Accès réservé aux administrateurs et managers');
            redirect('/utilisateur/dashboard');
        }

        $con = getDatabaseConnection();

        // Récupérer la configuration actuelle
        $stmt = $con->prepare("
            SELECT dgi_api_key, dgi_ifu, dgi_nim, dgi_mode, dgi_actif
            FROM societes
            WHERE id_societe = ?
        ");
        $stmt->execute([getSocieteActive()]);
        $config = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->render('societe/configuration-dgi', [
            'config' => $config ?: []
        ]);
    }

    /**
     * Sauvegarder la configuration DGI du client
     * Accessible par : admin, manager
     */
    public function sauvegarder_config_dgi()
    {
        // Vérification : Admin ou Manager
        if (!in_array($_SESSION['role'], ['admin', 'manager'])) {
            setFlash('danger', 'Accès réservé aux administrateurs et managers');
            redirect('/utilisateur/dashboard');
        }

        if (!$this->isPostRequest()) {
            redirect('/societe/configuration_dgi');
        }

        if (!CSRF::verifyRequest()) {
            setFlash('error', 'Token CSRF invalide');
            redirect('/societe/configuration_dgi');
        }

        $con = getDatabaseConnection();

        try {
            $stmt = $con->prepare("
                UPDATE societes SET
                    dgi_api_key = ?,
                    dgi_ifu = ?,
                    dgi_nim = ?,
                    dgi_mode = ?,
                    dgi_actif = ?
                WHERE id_societe = ?
            ");

            $stmt->execute([
                $_POST['dgi_api_key'] ?? null,
                $_POST['dgi_ifu'] ?? null,
                $_POST['dgi_nim'] ?? null,
                $_POST['dgi_mode'] ?? 'test',
                isset($_POST['dgi_actif']) ? 1 : 0,
                getSocieteActive()
            ]);

            Logger::info('Configuration DGI mise à jour', [
                'id_societe' => getSocieteActive(),
                'mode' => $_POST['dgi_mode'] ?? 'test',
                'actif' => isset($_POST['dgi_actif']),
                'par' => $_SESSION['utilisateur_id']
            ]);

            setFlash('success', 'Configuration DGI enregistrée avec succès !');
        } catch (Exception $e) {
            Logger::error('Erreur sauvegarde config DGI', ['error' => $e->getMessage()]);
            setFlash('error', 'Erreur lors de la sauvegarde');
        }

        redirect('/societe/configuration_dgi');
    }
}

class_alias('SocieteController', 'societe');
