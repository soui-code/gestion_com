<?php
class VenteModel
{
    public $con;

    public function __construct()
    {
        $this->con = getDatabaseConnection();
    }

    // Recherche produit (autocomplete)
    public function rechercheProduit($term)
    {
        $sql = "SELECT reference, designation, prix_vente, stock, photo, photo_type 
                FROM produits 
                WHERE (designation LIKE :term OR reference LIKE :term) 
                  AND etat = 'actif' AND stock > 0 
                LIMIT 10";
        $stmt = $this->con->prepare($sql);
        $stmt->execute([':term' => "%$term%"]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($results as &$r) {
            if ($r['photo']) {
                $r['photo_base64'] = base64_encode($r['photo']);
                unset($r['photo']); // Clean up to reduce payload size
            }
        }
        return $results;
    }

    // Récupérer tous les produits pour la grille POS (Limité pour la performance)
    public function getTousProduits()
    {
        return $this->con->query("
            SELECT reference, designation, prix_vente, stock, photo, photo_type 
            FROM produits 
            WHERE etat = 'actif' AND stock > 0 
            ORDER BY designation
            LIMIT 20
        ")->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer toutes les taxes actives
    public function getTaxes()
    {
        return $this->con->query("SELECT code_taxe, titre_taxe, taux_taxe, action_taxe FROM taxes WHERE etat_taxe = 'active'")->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer tous les clients
    public function getClients()
    {
        return $this->con->query("SELECT code_client, nom_prenom_client, telephone_client FROM clients WHERE etat_client = 'actif' ORDER BY nom_prenom_client")->fetchAll(PDO::FETCH_ASSOC);
    }

    // Valider la vente (transaction atomique)
    public function validerVente($panier, $client_id, $taxe_code, $remise, $montant_paye, $mode_paiement, $utilisateur_id)
    {
        try {
            $this->con->beginTransaction();

            $total_ht = 0;
            foreach ($panier as $item) {
                $total_ht += $item['prix'] * $item['qte'];
            }

            // Appliquer taxe
            $taxe = $this->con->query("SELECT taux_taxe, action_taxe FROM taxes WHERE code_taxe = " . (int)$taxe_code)->fetch();
            $montant_taxe = 0;
            if ($taxe) {
                $montant_taxe = $total_ht * ($taxe['taux_taxe'] / 100);
                if ($taxe['action_taxe'] === 'soustraire') {
                    $montant_taxe = -$montant_taxe;
                }
            }

            $total_ttc = $total_ht + $montant_taxe - $remise;
            $reste = $total_ttc - $montant_paye;
            $facture_id = $this->con->lastInsertId();

            // 2. Générer le numéro de facture standardisé (LINK) basé sur l'ID réel
            // Génère un nombre entre 10 000 000 et 99 999 999 (8 chiffres garantis)
            $random_id = random_int(10000000, 99999999);

            $numero_facture_string = 'FACT-' . date('Ymd') . '-' . $random_id;
            // 1. Créer la facture d'abord pour obtenir l'ID réel
            $stmtFacture = $this->con->prepare("INSERT INTO factures 
                (titre_facture, date_facture, type_facture, montant_ht, code_taxe, montant_ttc, code_client, etat_facture, numero_facture) 
                VALUES ('Vente directe', CURDATE(), 'vente', ?, ?, ?, ?, 'payée', ?)");
            $stmtFacture->execute([$total_ht, $taxe_code ?: null, $total_ttc, $client_id ?: null, $numero_facture_string]);
            

            // 3. Créer les lignes de commande
            foreach ($panier as $item) {
                $sql = "INSERT INTO commandes 
                    (date_commande, heure_commande, prix_achat, prix_vente, prix_commande, quantite_commande, reference, code_client, utilisateur_id, statut_commande, etat_commande, numero_facture)
                    VALUES (CURDATE(), CURTIME(), 0, :prix_vente, :prix_vente, :qte, :ref, :client, :user, 'Vente', 'validée', :facture)";

                $stmt = $this->con->prepare($sql);
                $stmt->execute([
                    ':prix_vente' => $item['prix'],
                    ':qte' => $item['qte'],
                    ':ref' => $item['ref'],
                    ':client' => $client_id ?: null,
                    ':user' => $utilisateur_id,
                    ':facture' => $numero_facture_string
                ]);

                // 4. Mettre à jour le stock
                $this->con->prepare("UPDATE produits SET stock = stock - ? WHERE reference = ?")
                    ->execute([(int)$item['qte'], $item['ref']]);

                // 5. Loguer le mouvement de sortie
                $motif = "Vente " . $numero_facture_string;
                $req_mouv = $this->con->prepare("INSERT INTO mouvements_stock (reference_produit, type_mouvement, quantite, utilisateur_id, motif) VALUES (?, 'sortie', ?, ?, ?)");
                $req_mouv->execute([$item['ref'], (int)$item['qte'], $utilisateur_id, $motif]);
            }

            // 6. Créer la transaction (paiement)
            $this->con->prepare("INSERT INTO transactions 
                (montant_transaction, mode_reglement, numero_facture, utilisateur_id) 
                VALUES (?, ?, ?, ?)")
                ->execute([$montant_paye, $mode_paiement, $numero_facture_string, $utilisateur_id]);

            $this->con->commit();
            return ['success' => true, 'facture' => $numero_facture_string, 'reste' => $reste];
        }
        catch (Exception $e) {
            $this->con->rollBack();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}