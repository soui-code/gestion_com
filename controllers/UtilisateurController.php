<?php
// Session et BaseController déjà chargés par bootstrap.php
require_once 'models/utilisateur.php';

class UtilisateurController extends BaseController
{
    public $message = "";

    function connexion()
    {
        $utilisateur = new UtilisateurModel();

        if (isset($_POST['btn-connexion'])) {
            $login = trim($_POST['sai_login'] ?? '');
            $mdp = $_POST['sai_mdp'] ?? '';

            $validator = $this->validate($_POST, [
                'sai_login' => 'required|min:1',
                'sai_mdp' => 'required|min:1'
            ]);

            if ($validator) {
                $this->message = $validator->firstError();
                $this->render('utilisateur/connexion', ['message' => $this->message]);
                return;
            }

            try {
                $result = $utilisateur->ConnexionUtilisateur($login);

                $auth_success = false;
                $needs_upgrade = false;

                if (!empty($result) && $result['etat'] == 'actif') {
                    if (password_verify($mdp, $result['mdp'])) {
                        $auth_success = true;
                    } elseif (strlen($result['mdp']) === 40 && sha1($mdp) === $result['mdp']) {
                        $auth_success = true;
                        $needs_upgrade = true;
                    }
                }

                if ($auth_success) {
                    if ($needs_upgrade) {
                        try {
                            $nouveauHash = password_hash($mdp, PASSWORD_BCRYPT);
                            $utilisateur->MiseAJourMotDePasse($result['utilisateur_id'], $nouveauHash);
                            Logger::info('Mot de passe migré vers bcrypt', ['utilisateur_id' => $result['utilisateur_id']]);
                        } catch (Exception $ex) {
                            Logger::warning('Échec migration mot de passe', ['error' => $ex->getMessage()]);
                        }
                    }

                    if (in_array($result['role'], ['admin', 'manager', 'caissier'])) {
                        // VÉRIFICATION ABONNEMENT pour Manager/Caissier
                        if ($result['role'] !== 'admin' && !empty($result['id_societe'])) {
                            $con = getDatabaseConnection();
                            $stmt = $con->prepare("
                                SELECT statut_abonnement, abonnement_expire_le, nom_societe
                                FROM societes
                                WHERE id_societe = ?
                            ");
                            $stmt->execute([$result['id_societe']]);
                            $societe = $stmt->fetch(PDO::FETCH_ASSOC);

                            if ($societe && $societe['statut_abonnement'] === 'expire') {
                                $this->message = "Abonnement expiré ! Contactez l'administrateur.";
                                Logger::warning('Connexion refusée - abonnement expiré', [
                                    'login' => $login,
                                    'societe' => $societe['nom_societe'],
                                    'expire_le' => $societe['abonnement_expire_le']
                                ]);
                                $this->render('utilisateur/abonnement-expire', [
                                    'societe' => $societe,
                                    'message' => $this->message
                                ]);
                                return;
                            }
                        }

                        session_regenerate_id(true);

                        $_SESSION['utilisateur_id'] = $result['utilisateur_id'];
                        $_SESSION['nom'] = $result['nom'];
                        $_SESSION['login'] = $result['login'];
                        $_SESSION['role'] = $result['role'];
                        $_SESSION['etat'] = $result['etat'];
                        $_SESSION['id_societe'] = ($result['role'] === 'admin') ? null : $result['id_societe'];
                        $_SESSION['id_boutique'] = ($result['role'] === 'admin') ? null : $result['id_boutique'];
                        $_SESSION['last_activity'] = time();

                        Logger::info('Connexion réussie', [
                            'utilisateur_id' => $result['utilisateur_id'],
                            'login' => $login,
                            'role' => $result['role'],
                            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
                        ]);

                        redirect('utilisateur/dashboard');
                    } else {
                        $this->message = "Rôle non autorisé";
                        Logger::warning('Tentative de connexion avec rôle non autorisé', [
                            'login' => $login,
                            'role' => $result['role']
                        ]);
                    }
                } else {
                    $this->message = "Login ou mot de passe incorrect";
                    Logger::warning('Tentative de connexion échouée', [
                        'login' => $login,
                        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
                    ]);
                    sleep(1);
                }
            } catch (Exception $e) {
                Logger::logException($e, ['login' => $login]);
                $this->message = isDebug() ? "Erreur de connexion : " . $e->getMessage() : "Erreur de connexion";
            }
        }

        $message = $this->message;
        $this->render('utilisateur/connexion', [
            'message' => $message,
        ]);
    }

    function dashboard()
    {
        $role = $_SESSION['role'] ?? 'caissier';

        // GESTION MANAGER : Redirection selon boutique_active
        if ($role === 'manager') {
            $boutique_active = $_SESSION['boutique_active'] ?? null;

            // Si aucune boutique active, rediriger vers la sélection
            if ($boutique_active === null) {
                redirect('/boutique/selectionner');
            }

            // Si boutique active définie, afficher le dashboard boutique
            // (continuer vers le code dashboard manager/caissier ci-dessous)
        }

        // GESTION CAISSIER : Initialiser boutique_active depuis id_boutique
        if ($role === 'caissier') {
            $id_boutique = $_SESSION['id_boutique'] ?? null;

            if ($id_boutique) {
                $_SESSION['boutique_active'] = $id_boutique;

                // Récupérer le nom de la boutique
                if (empty($_SESSION['boutique_active_nom'])) {
                    $con = getDatabaseConnection();
                    $stmt = $con->prepare("SELECT nom_boutique FROM boutiques WHERE code_boutique = ?");
                    $stmt->execute([$id_boutique]);
                    $boutique = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($boutique) {
                        $_SESSION['boutique_active_nom'] = $boutique['nom_boutique'];
                    }
                }
            }
        }

        // Dashboard différent selon le rôle
        if ($role === 'admin') {
            // Dashboard Admin : Vue globale de toutes les sociétés
            $con = getDatabaseConnection();

            // Statistiques globales
            $stats = [
                'total_boutiques' => $con->query("SELECT COUNT(*) FROM boutiques")->fetchColumn(),
                'total_utilisateurs' => $con->query("SELECT COUNT(*) FROM utilisateurs")->fetchColumn(),
                'total_produits' => $con->query("SELECT COUNT(*) FROM produits")->fetchColumn(),
            ];

            // Liste des sociétés avec leurs stats
            $query = "
                SELECT
                    s.*,
                    COUNT(DISTINCT b.code_boutique) as nb_boutiques,
                    COUNT(DISTINCT u.utilisateur_id) as nb_utilisateurs,
                    COUNT(DISTINCT p.reference) as nb_produits
                FROM societes s
                LEFT JOIN boutiques b ON s.id_societe = b.id_societe
                LEFT JOIN utilisateurs u ON s.id_societe = u.id_societe
                LEFT JOIN produits p ON s.id_societe = p.id_societe
                GROUP BY s.id_societe
                ORDER BY s.nom_societe
            ";
            $societes = $con->query($query)->fetchAll(PDO::FETCH_ASSOC);

            $this->render('utilisateur/dashboard-admin', [
                'stats' => $stats,
                'societes' => $societes
            ]);

        } else {
            // Dashboard Manager/Caissier : Stats de leur société/boutique
            require_once './models/dashboard.php';
            $dashboardModel = new DashboardModel();
            $dashboardModel->id_societe = getSocieteActive() ?? null;

            // Utiliser boutique_active au lieu de id_boutique
            $dashboardModel->id_boutique = $_SESSION['boutique_active'] ?? null;

            $stats = $dashboardModel->getStats();

            $this->render('utilisateur/tableau de bord', [
                'stats' => $stats
            ]);
        }
    }

    function ventes_mensuelles()
    {
        $this->render('utilisateur/ajax/ventes_mensuelles');
    }

    function modes_paiement()
    {
        $this->render('utilisateur/ajax/modes_paiement');
    }

    function enregistrement()
    {
        $utilisateur = new UtilisateurModel();
        $result = [];

        $utilisateur->id_societe = getSocieteActive() ?? null;

        // Récupérer les sociétés pour l'admin ou les boutiques pour le manager
        $societes = [];
        $boutiques = [];
        $role = $_SESSION['role'] ?? 'caissier';

        if ($role === 'admin') {
            // Admin peut voir toutes les sociétés
            $con = getDatabaseConnection();
            $societes = $con->query("SELECT id_societe, nom_societe FROM societes ORDER BY nom_societe")->fetchAll(PDO::FETCH_ASSOC);
        } elseif ($role === 'manager' && getSocieteActive()) {
            // Manager voit ses boutiques
            $con = getDatabaseConnection();
            $stmt = $con->prepare("SELECT code_boutique, nom_boutique FROM boutiques WHERE id_societe = :id_societe ORDER BY nom_boutique");
            $stmt->execute(['id_societe' => getSocieteActive()]);
            $boutiques = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        if (isset($_POST['btn-recherche'])) {
            $nom = $_POST['sai_recherche'] ?? '';
            try {
                $result = $utilisateur->Recherche($nom);
                if (empty($result)) {
                    setFlash('info', 'Aucun utilisateur trouvé avec ce nom');
                }
            } catch (Exception $e) {
                setFlash('danger', $e->getMessage());
            }
        } else {
            try {
                $result = $utilisateur->ListeUtilisateur();
            } catch (Exception $e) {
                setFlash('danger', $e->getMessage());
                $result = [];
            }
        }

        // Suppression sécurisée (POST + vérification)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn-supprimer'])) {
            if ($_SESSION['role'] !== 'manager') {
                setFlash('danger', 'Non autorisé: seul un manager peut supprimer des utilisateurs');
            } else {
                $id = (int)($_POST['id_utilisateur'] ?? 0);
                if ($id > 0) {
                    try {
                        $utilisateur->utilisateur_id = $id;
                        $utilisateur->SupprimerUtilisateur();
                        setFlash('success', 'Utilisateur supprimé avec succès');
                    } catch (Exception $e) {
                        setFlash('danger', $e->getMessage());
                    }
                }
            }
            redirect('/utilisateur/enregistrement');
        }

        // Ajout d'utilisateur
        if (isset($_POST['btn-ajouter'])) {
            if ($_SESSION['role'] !== 'manager' && $_SESSION['role'] !== 'admin') {
                Logger::warning('Tentative d\'ajout utilisateur non autorisée', [
                    'utilisateur_id' => $_SESSION['utilisateur_id'],
                    'role' => $_SESSION['role']
                ]);
                setFlash('danger', 'Non autorisé: seul un manager peut ajouter des utilisateurs');
            } else {
                $validator = $this->validate($_POST, [
                    'sai_login' => 'required|min:3|max:50|unique:utilisateurs,login',
                    'sai_nom' => 'required|min:3|max:100',
                    'sai_role' => 'required|in:admin,manager,caissier',
                    'sai_numero' => 'required|min:8|max:20'
                ]);

                if ($validator) {
                    saveOldInput();
                    setFlash('danger', $validator->firstError());
                } else {
                    try {
                        $login = trim($_POST['sai_login']);
                        $nom = trim($_POST['sai_nom']);
                        $role = $_POST['sai_role'];
                        $numero = trim($_POST['sai_numero']);
                        $sai_mdp = $_POST['sai_mdp'] ?? '';

                        $utilisateur->login = $login;
                        $utilisateur->nom = $nom;

                        if (!empty($sai_mdp)) {
                            if (strlen($sai_mdp) < 6) {
                                setFlash('danger', 'Le mot de passe doit contenir au moins 6 caractères');
                                redirect('/utilisateur/enregistrement');
                                return;
                            }
                            $utilisateur->mdp = password_hash($sai_mdp, PASSWORD_BCRYPT);
                            $msg_mdp = '';
                        } else {
                            $mdp_temp = bin2hex(random_bytes(4));
                            $utilisateur->mdp = password_hash('gestion_' . $mdp_temp, PASSWORD_BCRYPT);
                            $msg_mdp = ' (Mot de passe par défaut: gestion_' . $mdp_temp . ')';
                        }

                        $utilisateur->role = $role;
                        $utilisateur->numero = $numero;
                        $utilisateur->etat = "actif";

                        // Gestion intelligente de société/boutique selon le rôle du créateur
                        if ($_SESSION['role'] === 'admin') {
                            // Admin crée un manager ou admin → Assigner la société choisie
                            if ($role === 'manager' || $role === 'admin') {
                                $utilisateur->id_societe = $_POST['id_societe'] ?? null;
                                $utilisateur->id_boutique = null;
                            } else {
                                // Admin crée un caissier → Doit choisir société ET boutique (rare)
                                $utilisateur->id_societe = $_POST['id_societe'] ?? null;
                                $utilisateur->id_boutique = $_POST['id_boutique'] ?? null;
                            }
                        } elseif ($_SESSION['role'] === 'manager') {
                            // Manager crée un caissier → Hérite de la société du manager, choisit la boutique
                            $utilisateur->id_societe = getSocieteActive();
                            if ($role === 'caissier') {
                                $utilisateur->id_boutique = $_POST['id_boutique'] ?? null;
                            } else {
                                $utilisateur->id_boutique = null;
                            }
                        } else {
                            // Fallback : Hériter de la session
                            $utilisateur->id_societe = getSocieteActive() ?? null;
                            $utilisateur->id_boutique = $_SESSION['id_boutique'] ?? null;
                        }

                        $utilisateur->AjoutUtilisateur();
                        clearOldInput();

                        Logger::info('Nouvel utilisateur créé', [
                            'login' => $login,
                            'role' => $role,
                            'created_by' => $_SESSION['utilisateur_id']
                        ]);

                        setFlash('success', 'Utilisateur créé avec succès !' . $msg_mdp);
                    } catch (Exception $e) {
                        Logger::logException($e);
                        setFlash('danger', isDebug() ? $e->getMessage() : 'Erreur lors de la création de l\'utilisateur');
                    }
                }
            }
            redirect('/utilisateur/enregistrement');
        }

        // Modification d'utilisateur
        if (isset($_POST['btn-modifier'])) {
            if ($_SESSION['role'] !== 'manager' && $_SESSION['role'] !== 'admin') {
                Logger::warning('Tentative de modification utilisateur non autorisée', [
                    'utilisateur_id' => $_SESSION['utilisateur_id'],
                    'role' => $_SESSION['role']
                ]);
                setFlash('danger', 'Non autorisé: seul un manager peut modifier des utilisateurs');
            } else {
                $validator = $this->validate($_POST, [
                    'id_utilisateur' => 'required|integer',
                    'sai_login' => 'required|min:3|max:50',
                    'sai_nom' => 'required|min:3|max:100',
                    'sai_role' => 'required|in:admin,manager,caissier',
                    'sai_numero' => 'required|min:8|max:20',
                    'sai_etat' => 'required|in:actif,inactif'
                ]);

                if ($validator) {
                    saveOldInput();
                    setFlash('danger', $validator->firstError());
                } else {
                    try {
                        $id = (int)$_POST['id_utilisateur'];
                        $login = trim($_POST['sai_login']);
                        $nom = trim($_POST['sai_nom']);
                        $role = $_POST['sai_role'];
                        $numero = trim($_POST['sai_numero']);
                        $etat = $_POST['sai_etat'];
                        $nouveau_mdp = trim($_POST['sai_nouveau_mdp'] ?? '');

                        $utilisateur->utilisateur_id = $id;
                        $utilisateur->login = $login;
                        $utilisateur->nom = $nom;
                        $utilisateur->role = $role;
                        $utilisateur->numero = $numero;
                        $utilisateur->etat = $etat;
                        $utilisateur->id_societe = getSocieteActive() ?? null;
                        $utilisateur->id_boutique = $_POST['id_boutique'] ?? $_SESSION['id_boutique'] ?? null;

                        $utilisateur->ModificationUtilisateur();

                        // Changement de mot de passe si fourni
                        if (!empty($nouveau_mdp)) {
                            if (strlen($nouveau_mdp) < 6) {
                                setFlash('warning', 'Utilisateur modifié, mais le mot de passe doit contenir au moins 6 caractères');
                            } else {
                                $nouveauHash = password_hash($nouveau_mdp, PASSWORD_BCRYPT);
                                $utilisateur->MiseAJourMotDePasse($id, $nouveauHash);

                                Logger::info('Mot de passe modifié par manager', [
                                    'utilisateur_modifie' => $id,
                                    'modifie_par' => $_SESSION['utilisateur_id']
                                ]);

                                setFlash('success', 'Utilisateur et mot de passe modifiés avec succès');
                            }
                        } else {
                            setFlash('success', 'Utilisateur modifié avec succès');
                        }

                        clearOldInput();

                        Logger::info('Utilisateur modifié', [
                            'utilisateur_id' => $id,
                            'login' => $login,
                            'modifie_par' => $_SESSION['utilisateur_id']
                        ]);

                    } catch (Exception $e) {
                        Logger::logException($e);
                        setFlash('danger', isDebug() ? $e->getMessage() : 'Erreur lors de la modification');
                    }
                }
            }
            redirect('/utilisateur/enregistrement');
        }

        $this->render('utilisateur/enregistrement-utilisateur', [
            'result' => $result,
            'societes' => $societes,
            'boutiques' => $boutiques,
        ]);
    }

    function motdepasse()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn-changer-mdp'])) {
            $validator = $this->validate($_POST, [
                'sai_ancien_mdp' => 'required',
                'sai_nouveau_mdp' => 'required|min:6',
                'sai_confirmation_mdp' => 'required'
            ]);

            if ($validator) {
                setFlash('danger', $validator->firstError());
            } else {
                $ancien = $_POST['sai_ancien_mdp'];
                $nouveau = $_POST['sai_nouveau_mdp'];
                $confirmation = $_POST['sai_confirmation_mdp'];

                if ($nouveau !== $confirmation) {
                    setFlash('danger', 'Les nouveaux mots de passe ne correspondent pas');
                } elseif ($ancien === $nouveau) {
                    setFlash('warning', 'Le nouveau mot de passe doit être différent de l\'ancien');
                } else {
                    try {
                        $utilisateur = new UtilisateurModel();
                        $result = $utilisateur->ConnexionUtilisateur($_SESSION['login']);

                        if (!empty($result) && password_verify($ancien, $result['mdp'])) {
                            $nouveauHash = password_hash($nouveau, PASSWORD_BCRYPT);
                            $utilisateur->MiseAJourMotDePasse($_SESSION['utilisateur_id'], $nouveauHash);

                            Logger::info('Mot de passe modifié', [
                                'utilisateur_id' => $_SESSION['utilisateur_id'],
                                'login' => $_SESSION['login']
                            ]);

                            setFlash('success', 'Mot de passe changé avec succès');
                        } else {
                            Logger::warning('Tentative de changement de mot de passe avec ancien mot de passe incorrect', [
                                'utilisateur_id' => $_SESSION['utilisateur_id']
                            ]);
                            setFlash('danger', 'Mot de passe actuel incorrect');
                        }
                    } catch (Exception $e) {
                        Logger::logException($e);
                        setFlash('danger', isDebug() ? 'Erreur: ' . $e->getMessage() : 'Une erreur est survenue');
                    }
                }
            }
            redirect('/utilisateur/motdepasse');
        }

        $this->render('utilisateur/motdepasse');
    }

    function switchSociete()
    {
        // Uniquement pour les admins
        if ($_SESSION['role'] !== 'admin') {
            setFlash('danger', 'Accès refusé');
            redirect('/utilisateur/dashboard');
        }

        $id_societe = (int)($_GET['id'] ?? 0);

        if ($id_societe > 0) {
            // Vérifier que la société existe
            $con = getDatabaseConnection();
            $stmt = $con->prepare("SELECT nom_societe FROM societes WHERE id_societe = ?");
            $stmt->execute([$id_societe]);
            $societe = $stmt->fetch();

            if ($societe) {
                // Mettre en session temporaire pour consultation
                $_SESSION['admin_view_societe'] = $id_societe;
                $_SESSION['admin_view_societe_nom'] = $societe['nom_societe'];

                Logger::info('Admin consulte une société', [
                    'admin_id' => $_SESSION['utilisateur_id'],
                    'societe_id' => $id_societe,
                    'societe_nom' => $societe['nom_societe']
                ]);

                setFlash('info', 'Vous consultez maintenant : ' . $societe['nom_societe']);
                redirect('/produit/liste'); // ou une autre page
            } else {
                setFlash('danger', 'Société introuvable');
                redirect('/utilisateur/dashboard');
            }
        } else {
            // Retour au mode admin normal
            unset($_SESSION['admin_view_societe']);
            unset($_SESSION['admin_view_societe_nom']);
            setFlash('info', 'Retour à la vue administrateur');
            redirect('/utilisateur/dashboard');
        }
    }

    /**
     * Afficher les logs de l'application (Admin uniquement)
     */
    public function logs()
    {
        // Vérification : Admin uniquement
        if ($_SESSION['role'] !== 'admin') {
            setFlash('danger', 'Accès réservé aux administrateurs');
            redirect('/utilisateur/dashboard');
        }

        $logFile = __DIR__ . '/../logs/app.log';

        if (!file_exists($logFile)) {
            setFlash('warning', 'Aucun fichier de log trouvé');
            redirect('/utilisateur/dashboard');
        }

        // Lire les dernières lignes du fichier (plus récentes en premier)
        $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $lines = array_reverse($lines); // Plus récent en premier

        // Limiter à 500 dernières lignes pour éviter surcharge
        $lines = array_slice($lines, 0, 500);

        $this->render('utilisateur/logs', [
            'logs' => $lines,
            'total' => count($lines),
            'file_size' => filesize($logFile),
            'last_modified' => filemtime($logFile)
        ]);
    }
}

// Alias pour compatibilité avec le router
class_alias('UtilisateurController', 'utilisateur');