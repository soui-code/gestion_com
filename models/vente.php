<?php
class VenteModel
{
    public $con;

    public function __construct()
    {
        $this->con = getDatabaseConnection();
    }

    // Recherche produit (autocomplete)
    public function rechercheProduit($term, $id_societe = null, $id_boutique = null)
    {
        $sql = "SELECT reference, designation, prix_vente, stock, photo, photo_type 
                FROM produits 
                WHERE (designation LIKE :term OR reference LIKE :term) 
                  AND etat = 'actif' AND stock > 0";
        if (!empty($id_societe)) {
            $sql .= " AND id_societe = :id_societe";
        }
        if (!empty($id_boutique)) {
            $sql .= " AND id_boutique = :id_boutique";
        }
        $sql .= " LIMIT 10";
        $stmt = $this->con->prepare($sql);
        $params = [':term' => "%$term%"];
        if (!empty($id_societe)) {
            $params[':id_societe'] = $id_societe;
        }
        if (!empty($id_boutique)) {
            $params[':id_boutique'] = $id_boutique;
        }
        $stmt->execute($params);
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
    public function getTousProduits($id_societe = null, $id_boutique = null)
    {
        $sql = "
            SELECT reference, designation, prix_vente, stock, photo, photo_type, id_boutique
            FROM produits
            WHERE etat = 'actif' AND stock > 0
        ";

        if (!empty($id_societe)) {
            $sql .= " AND id_societe = :id_societe";
        }

        // IMPORTANT : Les caissiers voient uniquement leur boutique
        // Les managers/admins voient toute la société
        if (!empty($id_boutique) && isset($_SESSION['role']) && $_SESSION['role'] === 'caissier') {
            $sql .= " AND id_boutique = :id_boutique";
        }

        $sql .= " ORDER BY designation LIMIT 50";
        $stmt = $this->con->prepare($sql);
        $params = [];

        if (!empty($id_societe)) {
            $params[':id_societe'] = $id_societe;
        }
        if (!empty($id_boutique) && isset($_SESSION['role']) && $_SESSION['role'] === 'caissier') {
            $params[':id_boutique'] = $id_boutique;
        }

        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer toutes les taxes actives
    public function getTaxes($id_societe = null)
    {
        $stmt = $this->con->prepare("SELECT code_taxe, titre_taxe, taux_taxe, action_taxe FROM taxes WHERE etat_taxe = 'active' AND id_societe = :id_societe");
        $stmt->execute([':id_societe' => $id_societe]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer tous les clients
    public function getClients($id_societe = null, $id_boutique = null)
    {
        $sql = "SELECT code_client, nom_prenom_client, telephone_client FROM clients WHERE etat_client = 'actif'";
        if (!empty($id_societe)) {
            $sql .= " AND id_societe = :id_societe";
        }
        if (!empty($id_boutique)) {
            $sql .= " AND id_boutique = :id_boutique";
        }
        $sql .= " ORDER BY nom_prenom_client";
        $stmt = $this->con->prepare($sql);
        $params = [];
        if (!empty($id_societe)) {
            $params[':id_societe'] = $id_societe;
        }
        if (!empty($id_boutique)) {
            $params[':id_boutique'] = $id_boutique;
        }
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Valider la vente (transaction atomique)
    public function validerVente($panier, $client_id, $taxes_array, $remise, $montant_paye, $mode_paiement, $utilisateur_id, $id_societe = null, $id_boutique = null)
    {
        try {
            $this->con->beginTransaction();

            // La colonne ventes.code_client est NOT NULL : si aucun client n'a été
            // sélectionné (vente "au comptoir"), on résout/crée le client générique
            // "Client de passage" propre à la société active, plutôt que d'envoyer null.
            if (empty($client_id)) {
                require_once 'models/client.php';
                $clientModel = new ClientModel();
                $clientModel->con = $this->con; // partager la même connexion/transaction
                $client_id = $clientModel->getOuCreerClientPassage($id_societe, $id_boutique);
            }

            // 1. Calculer le montant TTC (les prix dans le panier sont TTC)
            $total_ttc_avant_remise = 0;
            foreach ($panier as $item) {
                $total_ttc_avant_remise += $item['prix'] * $item['qte'];
            }

            // 2. Calculer le taux de TVA total
            $tauxTVA = 0;
            if (!empty($taxes_array) && is_array($taxes_array)) {
                foreach ($taxes_array as $taxe) {
                    $taux = floatval($taxe['taux'] ?? 0);
                    $action = $taxe['action'] ?? 'ajouter';

                    if ($action === 'ajouter') {
                        $tauxTVA += $taux;
                    } else {
                        $tauxTVA -= $taux;
                    }
                }
            }

            // Si aucune taxe, on suppose TVA 18% par défaut
            if ($tauxTVA == 0) {
                $tauxTVA = 0.18;
            } else {
                // Diviser par 100 car le taux est stocké comme 18 (pour 18%) et non 0.18
                $tauxTVA = $tauxTVA / 100;
            }

            // 3. Appliquer la remise sur le TTC
            $total_ttc = $total_ttc_avant_remise - $remise;

            // 4. Extraire HT et TVA depuis le TTC
            // Formule : HT = TTC / (1 + taux_tva)
            // Formule : TVA = TTC - HT
            $total_ht = round($total_ttc / (1 + $tauxTVA), 2);
            $montant_tva = round($total_ttc - $total_ht, 2);

            $reste = $total_ttc - $montant_paye;

            // 3. Insérer la vente
            $stmtVente = $this->con->prepare("
                INSERT INTO ventes (
                    date_vente, date_heure_vente, code_client, montant_total,
                    montant_ht, montant_tva, montant_ttc, mode_paiement,
                    utilisateur_id, id_societe, id_boutique, taxes_json
                ) VALUES (CURDATE(), NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmtVente->execute([
                $client_id,
                $total_ttc,
                $total_ht,
                $montant_tva,
                $total_ttc,
                $mode_paiement,
                $utilisateur_id,
                $id_societe,
                $id_boutique,
                json_encode($taxes_array)
            ]);
            $id_vente = $this->con->lastInsertId();

            // 4. Insérer les détails de vente
            foreach ($panier as $item) {
                $stmt = $this->con->prepare("
                    INSERT INTO details_vente (
                        id_vente, reference_produit, quantite, prix_unitaire, total
                    ) VALUES (?, ?, ?, ?, ?)
                ");
                $total_ligne = $item['prix'] * $item['qte'];
                $stmt->execute([
                    $id_vente,
                    $item['ref'],
                    $item['qte'],
                    $item['prix'],
                    $total_ligne
                ]);

                // 5. Mettre à jour le stock
                $this->con->prepare("UPDATE produits SET stock = stock - ? WHERE reference = ? AND id_societe = ?")
                    ->execute([(int)$item['qte'], $item['ref'], $id_societe]);

                // 6. Loguer le mouvement de sortie
                $motif = "Vente #" . $id_vente;
                $req_mouv = $this->con->prepare("INSERT INTO mouvements_stock (reference_produit, type_mouvement, quantite, utilisateur_id, motif, id_societe, id_boutique) VALUES (?, 'sortie', ?, ?, ?, ?, ?)");
                $req_mouv->execute([$item['ref'], (int)$item['qte'], $utilisateur_id, $motif, $id_societe, $id_boutique]);
            }

            // 7. Créer la transaction (paiement)
            $stmtTrans = $this->con->prepare("
                INSERT INTO transactions (
                    montant_transaction, mode_reglement, id_vente, utilisateur_id,
                    etat_transaction, id_societe, id_boutique
                ) VALUES (?, ?, ?, ?, 'validée', ?, ?)
            ");
            $stmtTrans->execute([$montant_paye, $mode_paiement, $id_vente, $utilisateur_id, $id_societe, $id_boutique]);

            // 8. Créer la facture FNE automatiquement
            require_once 'models/facture_fne.php';
            // IMPORTANT : Passer la même connexion PDO pour partager la transaction
            $factureFNE = new FactureFNE($this->con);
            $resultFacture = $factureFNE->creerFactureDepuisVente($id_vente, $id_societe);

            // Si tout s'est bien passé, commit la transaction
            $this->con->commit();

            if ($resultFacture && $resultFacture['success']) {
                return [
                    'success' => true,
                    'id_vente' => $id_vente,
                    'facture' => $resultFacture['numero_facture'],
                    'numero_fiscal' => $resultFacture['numero_fiscal'],
                    'reste' => $reste
                ];
            } else {
                // Ne devrait jamais arriver ici car creerFactureDepuisVente lance une exception en cas d'erreur
                return [
                    'success' => true,
                    'id_vente' => $id_vente,
                    'facture' => null,
                    'reste' => $reste,
                    'warning' => 'Vente enregistrée mais facture FNE non générée'
                ];
            }
        }
        catch (Exception $e) {
            // Rollback seulement si une transaction est active
            if ($this->con->inTransaction()) {
                $this->con->rollBack();
            }
            Logger::error('Erreur validation vente', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}