<?php
require 'models/boutique.php';

class boutique extends BaseController{

    public function enregistrement(){
        $boutique=new boutiquemodel();
        $boutique->id_societe = getSocieteActive() ?? null;
        $result = $boutique->Listeboutique();

        if (isset($_POST['btn-ajouter-boutique'])) {
            // VÉRIFICATION LIMITE ABONNEMENT
            $id_societe = getSocieteActive();
            if ($id_societe) {
                $con = getDatabaseConnection();

                // Récupérer plan actuel
                $stmt = $con->prepare("
                    SELECT p.nb_boutiques_max, p.nom_plan
                    FROM societes s
                    LEFT JOIN plans p ON s.plan_actuel_id = p.id_plan
                    WHERE s.id_societe = ? AND s.statut_abonnement = 'actif'
                ");
                $stmt->execute([$id_societe]);
                $plan = $stmt->fetch(PDO::FETCH_ASSOC);

                // Compter boutiques existantes
                $stmt = $con->prepare("SELECT COUNT(*) FROM boutiques WHERE id_societe = ?");
                $stmt->execute([$id_societe]);
                $nb_boutiques = $stmt->fetchColumn();

                if ($plan && $nb_boutiques >= $plan['nb_boutiques_max']) {
                    setFlash('error', "Limite atteinte ! Votre plan {$plan['nom_plan']} autorise {$plan['nb_boutiques_max']} boutique(s) maximum. Contactez l'administrateur pour upgrader.");
                    redirect('/boutique/enregistrement');
                    return;
                }
            }

            $boutique->nom_boutique=$_POST['sai_nom_boutique'];
            $boutique->telephone_boutique=$_POST['sai_telephone_boutique'];
            $boutique->latitude=$_POST['sai_latitude'] ?: null;
            $boutique->longitude=$_POST['sai_longitude'] ?: null;
            $boutique->produit_json=$_POST['sai_produit_json'] ?: null;
            $boutique->etat_boutique=$_POST['sai_etat_boutique'];

            $boutique->AjoutBoutique();
            $code_boutique = $boutique->con->lastInsertId();

            // Import Excel → Produits (optionnel)
            $nb_produits = 0;
            if (!empty($boutique->produit_json)) {
                $nb_produits = $this->syncProductsFromJson($boutique->produit_json, $boutique->id_societe, $code_boutique);
            }

            if ($nb_produits > 0) {
                setFlash('success', "Boutique créée avec succès ! $nb_produits produits importés.");
            } else {
                setFlash('success', 'Boutique créée avec succès !');
            }
            redirect('/boutique/enregistrement');
        }

        
        if (isset($_POST['btn-modifier-boutique'])) {
            $boutique->nom_boutique=$_POST['sai_nom_boutique'];
            $boutique->telephone_boutique=$_POST['sai_telephone_boutique'];
            $boutique->latitude=$_POST['sai_latitude'];
            $boutique->longitude=$_POST['sai_longitude'];
            $boutique->produit_json=$_POST['sai_produit_json'];
            $boutique->etat_boutique=$_POST['sai_etat_boutique'];

            $boutique->code_boutique=$_POST['sai_code_boutique'];
            
            $boutique->ModificationBoutique();
            $this->syncProductsFromJson($boutique->produit_json, $boutique->id_societe, $boutique->code_boutique);
            setFlash('success', 'Boutique modifiée avec succès !');
            redirect('/boutique/enregistrement');
        }

        if (isset($_POST['btn-supprimer-boutique'])) {
            $boutique->code_boutique=$_POST['sai_code_boutique'];
            $boutique->SuppressionBoutique();
            setFlash('success', 'Boutique supprimée avec succès !');
            redirect('/boutique/enregistrement');
        }


        $this->render('boutique/enregistrement-boutique',[
            'result' => $result,
        ]);
    }

        public function Map(){
            $boutique = new boutiquemodel();
            $boutique->id_societe = getSocieteActive() ?? null;
            $boutiques = $boutique->getAllBoutiques();

        $this->render('boutique/map-boutique',[
            'boutiques' => $boutiques,
        ]);
    }

    public function transfert()
    {
        $boutique = new boutiquemodel();
        $boutique->id_societe = getSocieteActive() ?? null;
        $boutiques = $boutique->Listeboutique();

        // Historique récent des transferts
        $con = getDatabaseConnection();
        $stmt = $con->prepare("
            SELECT m.date_mouvement, m.quantite, p.designation as produit,
                   bs.nom_boutique as boutique_source, bd.nom_boutique as boutique_dest,
                   u.nom as utilisateur
            FROM mouvements_stock m
            JOIN produits p ON m.reference_produit = p.reference
            LEFT JOIN boutiques bs ON m.id_boutique = bs.code_boutique
            LEFT JOIN boutiques bd ON SUBSTRING_INDEX(m.motif, '#', -1) = bd.code_boutique
            JOIN utilisateurs u ON m.utilisateur_id = u.utilisateur_id
            WHERE m.motif LIKE 'Transfert%'
              AND m.id_societe = ?
            ORDER BY m.date_mouvement DESC
            LIMIT 20
        ");
        $stmt->execute([getSocieteActive()]);
        $historique = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->render('boutique/transfert-stock', [
            'boutiques' => $boutiques,
            'historique' => $historique
        ]);
    }

    // API: Produits d'une boutique
    public function produits()
    {
        header('Content-Type: application/json');

        $id_boutique = $_GET['id_boutique'] ?? null;
        $id_societe = getSocieteActive() ?? null;

        if (!$id_boutique || !$id_societe) {
            echo json_encode(['success' => false, 'produits' => []]);
            exit;
        }

        try {
            $con = getDatabaseConnection();
            $stmt = $con->prepare("SELECT reference, designation, stock FROM produits WHERE id_boutique = ? AND id_societe = ? AND etat = 'actif' AND stock > 0 ORDER BY designation");
            $stmt->execute([$id_boutique, $id_societe]);
            $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['success' => true, 'produits' => $produits]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'produits' => []]);
        }
        exit;
    }

    private function syncProductsFromJson($produit_json, $id_societe, $id_boutique = null)
    {
        if (empty($produit_json)) return 0;

        $produits = json_decode($produit_json, true);
        if (!is_array($produits) || count($produits) === 0) return 0;

        $count = 0;

        $con = getDatabaseConnection();
        $utilisateur_id = $_SESSION['utilisateur_id'] ?? null;

        foreach ($produits as $item) {
            $designation = $item['designation'] ?? '';
            if (empty($designation)) continue;

            $reference = $item['reference'] ?? '';
            $prix_achat = $item['prix_achat'] ?? 0;
            $prix_vente = $item['prix_vente'] ?? 0;
            $stock = $item['stock'] ?? 0;
            $stock_alert = $item['stock_alert'] ?? 5;
            $description = $item['description'] ?? '';
            $etat = $item['etat'] ?? 'actif';

            // Chercher si produit existe deja (par ref ou designation + societe)
            $existant = null;
            if (!empty($reference)) {
                $stmt = $con->prepare("SELECT reference, stock FROM produits WHERE (reference = :ref OR designation = :des) AND id_societe = :soc LIMIT 1");
                $stmt->execute([':ref' => $reference, ':des' => $designation, ':soc' => $id_societe]);
                $existant = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                $stmt = $con->prepare("SELECT reference, stock FROM produits WHERE designation = :des AND id_societe = :soc LIMIT 1");
                $stmt->execute([':des' => $designation, ':soc' => $id_societe]);
                $existant = $stmt->fetch(PDO::FETCH_ASSOC);
            }

            if ($existant) {
                // UPDATE — garder le stock existant + ajouter si qte nouvelle plus grande
                $nouveau_stock = max($existant['stock'], (int)$stock);
                $stmt = $con->prepare("UPDATE produits SET prix_achat=:pa, prix_vente=:pv, stock_alert=:sa, description=:desc, etat=:etat WHERE reference=:ref AND id_societe=:soc");
                $stmt->execute([
                    ':pa' => $prix_achat, ':pv' => $prix_vente, ':sa' => $stock_alert,
                    ':desc' => $description, ':etat' => $etat,
                    ':ref' => $existant['reference'], ':soc' => $id_societe
                ]);
            } else {
                // INSERT
                $stmt = $con->prepare("INSERT INTO produits (designation, prix_achat, prix_vente, stock, stock_alert, description, etat, id_societe, id_boutique) VALUES (:des, :pa, :pv, :stock, :sa, :desc, :etat, :soc, :boutique)");
                $stmt->execute([
                    ':des' => $designation, ':pa' => $prix_achat, ':pv' => $prix_vente,
                    ':stock' => (int)$stock, ':sa' => (int)$stock_alert,
                    ':desc' => $description, ':etat' => $etat, ':soc' => $id_societe,
                    ':boutique' => $id_boutique
                ]);
                $new_id = $con->lastInsertId();

                if ((int)$stock > 0) {
                    $con->prepare("INSERT INTO mouvements_stock (reference_produit, type_mouvement, quantite, utilisateur_id, motif, id_societe, id_boutique) VALUES (?, 'entree', ?, ?, 'Import boutique', ?, ?)")
                        ->execute([$new_id, (int)$stock, $utilisateur_id, $id_societe, $id_boutique]);
                }
                $count++;
            }
        }

        return $count;
    }

    // Transfert de stock entre boutiques
    public function transferer()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
            exit;
        }

        try {
            $reference = $_POST['reference'] ?? null;
            $boutique_source = $_POST['boutique_source'] ?? null;
            $boutique_destination = $_POST['boutique_destination'] ?? null;
            $quantite = (int)($_POST['quantite'] ?? 0);
            $id_societe = getSocieteActive() ?? null;
            $utilisateur_id = $_SESSION['utilisateur_id'] ?? null;

            // Validations
            if (!$reference || !$boutique_source || !$boutique_destination || $quantite <= 0) {
                throw new Exception('Données invalides');
            }

            if ($boutique_source == $boutique_destination) {
                throw new Exception('Les boutiques doivent être différentes');
            }

            $con = getDatabaseConnection();
            $con->beginTransaction();

            // 1. Vérifier stock source
            $stmt = $con->prepare("SELECT stock, designation FROM produits WHERE reference = ? AND id_boutique = ? AND id_societe = ?");
            $stmt->execute([$reference, $boutique_source, $id_societe]);
            $produit_source = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$produit_source) {
                throw new Exception('Produit introuvable dans la boutique source');
            }

            if ($produit_source['stock'] < $quantite) {
                throw new Exception("Stock insuffisant (disponible: {$produit_source['stock']})");
            }

            // 2. Diminuer stock source
            $con->prepare("UPDATE produits SET stock = stock - ? WHERE reference = ? AND id_boutique = ?")
                ->execute([$quantite, $reference, $boutique_source]);

            // 3. Vérifier si produit existe dans destination
            $stmt = $con->prepare("SELECT reference FROM produits WHERE reference = ? AND id_boutique = ? AND id_societe = ?");
            $stmt->execute([$reference, $boutique_destination, $id_societe]);
            $produit_dest = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($produit_dest) {
                // Augmenter stock
                $con->prepare("UPDATE produits SET stock = stock + ? WHERE reference = ? AND id_boutique = ?")
                    ->execute([$quantite, $reference, $boutique_destination]);
            } else {
                // Créer produit dans destination
                $stmt = $con->prepare("SELECT * FROM produits WHERE reference = ? AND id_boutique = ? LIMIT 1");
                $stmt->execute([$reference, $boutique_source]);
                $produit_model = $stmt->fetch(PDO::FETCH_ASSOC);

                // Vérifier que la catégorie existe dans la société (ou NULL)
                $id_categorie = null;
                if (!empty($produit_model['id_categorie'])) {
                    $stmt = $con->prepare("SELECT id_categorie FROM categories WHERE id_categorie = ? AND id_societe = ? LIMIT 1");
                    $stmt->execute([$produit_model['id_categorie'], $id_societe]);
                    $cat = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($cat) {
                        $id_categorie = $cat['id_categorie'];
                    }
                }

                // Vérifier que le fournisseur existe dans la société (ou NULL)
                $id_fournisseur = null;
                if (!empty($produit_model['id_fournisseur'])) {
                    $stmt = $con->prepare("SELECT id_fournisseur FROM fournisseurs WHERE id_fournisseur = ? AND id_societe = ? LIMIT 1");
                    $stmt->execute([$produit_model['id_fournisseur'], $id_societe]);
                    $fourn = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($fourn) {
                        $id_fournisseur = $fourn['id_fournisseur'];
                    }
                }

                $con->prepare("INSERT INTO produits (designation, id_categorie, id_fournisseur, prix_achat, prix_vente, stock, stock_alert, description, etat, id_societe, id_boutique)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")
                    ->execute([
                        $produit_model['designation'],
                        $id_categorie,
                        $id_fournisseur,
                        $produit_model['prix_achat'],
                        $produit_model['prix_vente'],
                        $quantite,
                        $produit_model['stock_alert'],
                        $produit_model['description'],
                        $produit_model['etat'],
                        $id_societe,
                        $boutique_destination
                    ]);
            }

            // 4. Logger les mouvements
            $motif = "Transfert vers boutique #$boutique_destination";
            $con->prepare("INSERT INTO mouvements_stock (reference_produit, type_mouvement, quantite, utilisateur_id, motif, id_societe, id_boutique)
                VALUES (?, 'sortie', ?, ?, ?, ?, ?)")
                ->execute([$reference, $quantite, $utilisateur_id, $motif, $id_societe, $boutique_source]);

            $motif = "Transfert depuis boutique #$boutique_source";
            $con->prepare("INSERT INTO mouvements_stock (reference_produit, type_mouvement, quantite, utilisateur_id, motif, id_societe, id_boutique)
                VALUES (?, 'entree', ?, ?, ?, ?, ?)")
                ->execute([$reference, $quantite, $utilisateur_id, $motif, $id_societe, $boutique_destination]);

            $con->commit();

            Logger::info('Transfert stock réussi', [
                'reference' => $reference,
                'de' => $boutique_source,
                'vers' => $boutique_destination,
                'quantite' => $quantite
            ]);

            echo json_encode(['success' => true, 'message' => "Transfert de $quantite {$produit_source['designation']} réussi"]);
        } catch (Exception $e) {
            if (isset($con)) $con->rollBack();
            Logger::error('Erreur transfert stock', ['error' => $e->getMessage()]);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

    /**
     * Page de sélection de boutique (manager uniquement)
     */
    public function selectionner()
    {
        // Vérification : Manager uniquement
        if ($_SESSION['role'] !== 'manager') {
            setFlash('danger', 'Accès refusé');
            redirect('/utilisateur/dashboard');
        }

        $boutique = new boutiquemodel();
        $boutique->id_societe = getSocieteActive();
        $boutiques = $boutique->Listeboutique();

        // Si aucune boutique, rediriger vers création
        if (empty($boutiques)) {
            setFlash('info', 'Aucune boutique enregistrée. Créez-en une pour commencer.');
            redirect('/boutique/enregistrement');
        }

        $this->render('boutique/selectionner', [
            'boutiques' => $boutiques
        ]);
    }

    /**
     * Activer une boutique (changer le contexte)
     */
    public function activer()
    {
        // Vérification : Manager uniquement
        if ($_SESSION['role'] !== 'manager') {
            setFlash('danger', 'Accès refusé');
            redirect('/utilisateur/dashboard');
        }

        $code_boutique = (int)($_GET['id'] ?? 0);

        if ($code_boutique <= 0) {
            setFlash('danger', 'Boutique invalide');
            redirect('/boutique/selectionner');
        }

        // Vérifier que la boutique appartient bien à la société du manager
        $con = getDatabaseConnection();
        $stmt = $con->prepare("SELECT code_boutique, nom_boutique FROM boutiques WHERE code_boutique = ? AND id_societe = ?");
        $stmt->execute([$code_boutique, getSocieteActive()]);
        $boutique = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$boutique) {
            setFlash('danger', 'Boutique introuvable ou non autorisée');
            redirect('/boutique/selectionner');
        }

        // Activer la boutique dans la session
        $_SESSION['boutique_active'] = $code_boutique;
        $_SESSION['boutique_active_nom'] = $boutique['nom_boutique'];

        Logger::info('Boutique activée', [
            'utilisateur_id' => $_SESSION['utilisateur_id'],
            'code_boutique' => $code_boutique,
            'nom_boutique' => $boutique['nom_boutique']
        ]);

        setFlash('success', "Boutique « {$boutique['nom_boutique']} » activée !");
        redirect('/utilisateur/dashboard');
    }

    /**
     * Désactiver la boutique (retour au menu société)
     */
    public function desactiver()
    {
        // Vérification : Manager uniquement
        if ($_SESSION['role'] !== 'manager') {
            setFlash('danger', 'Accès refusé');
            redirect('/utilisateur/dashboard');
        }

        $nom_boutique = $_SESSION['boutique_active_nom'] ?? 'Boutique';

        unset($_SESSION['boutique_active']);
        unset($_SESSION['boutique_active_nom']);

        Logger::info('Boutique désactivée', [
            'utilisateur_id' => $_SESSION['utilisateur_id'],
            'nom_boutique' => $nom_boutique
        ]);

        setFlash('info', "Boutique « {$nom_boutique} » désactivée. Retour au menu société.");
        redirect('/utilisateur/dashboard');
    }
}
