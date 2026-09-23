<?php

require_once 'config/montant_en_lettres.php';

// Intégration API DGI (optionnelle)
if (file_exists(__DIR__ . '/../config/api_dgi.php')) {
    require_once __DIR__ . '/../config/api_dgi.php';
}

class FactureFNE
{
    private $con;

    public function __construct($connection = null)
    {
        // Accepter une connexion existante ou en créer une nouvelle
        $this->con = $connection ?? getDatabaseConnection();
    }

    /**
     * Créer une facture normalisée depuis une vente
     *
     * @param int $idVente
     * @param int $idSociete
     * @return array|false
     */
    public function creerFactureDepuisVente($idVente, $idSociete)
    {
        try {
            // Note: Pas de beginTransaction() car cette méthode est appelée
            // depuis validerVente() qui a déjà ouvert une transaction

            // 1. Récupérer les informations de la vente
            $stmt = $this->con->prepare("
                SELECT v.*, c.nom_prenom_client, c.adresse_client, c.telephone_client, c.email_client,
                       c.type_client, c.raison_sociale, c.compte_contribuable, c.registre_commerce,
                       b.nom_boutique
                FROM ventes v
                LEFT JOIN clients c ON c.code_client = v.code_client
                LEFT JOIN boutiques b ON b.code_boutique = v.id_boutique
                WHERE v.id_vente = ? AND v.id_societe = ?
            ");
            $stmt->execute([$idVente, $idSociete]);
            $vente = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$vente) {
                throw new Exception("Vente introuvable");
            }

            // 2. Récupérer les détails de la vente
            $stmt = $this->con->prepare("
                SELECT dv.*, p.designation, p.reference
                FROM details_vente dv
                LEFT JOIN produits p ON p.reference = dv.reference_produit
                WHERE dv.id_vente = ?
            ");
            $stmt->execute([$idVente]);
            $details = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 3. Récupérer le taux de TVA total depuis les taxes JSON
            $tauxTVA = 0.18; // Par défaut 18%
            if (!empty($vente['taxes_json'])) {
                $taxes = json_decode($vente['taxes_json'], true);
                if (is_array($taxes) && count($taxes) > 0) {
                    $tauxTVA = 0;
                    foreach ($taxes as $taxe) {
                        $taux = floatval($taxe['taux'] ?? 0);
                        $action = $taxe['action'] ?? 'ajouter';
                        if ($action === 'ajouter') {
                            $tauxTVA += $taux;
                        } else {
                            $tauxTVA -= $taux;
                        }
                    }
                    if ($tauxTVA == 0) {
                        $tauxTVA = 0.18;
                    } else {
                        // Diviser par 100 car le taux est stocké comme 18 (pour 18%) et non 0.18
                        $tauxTVA = $tauxTVA / 100;
                    }
                }
            }

            // 4. Calculer montants HT, TVA, TTC (les prix unitaires sont TTC)
            $montantTTC = 0;
            $detailsAvecCalculs = [];

            foreach ($details as $detail) {
                $prixUnitaireTTC = $detail['prix_unitaire']; // Le prix stocké est TTC
                $quantite = $detail['quantite'];
                $totalTTC = $prixUnitaireTTC * $quantite;

                // Extraire le HT depuis le TTC : HT = TTC / (1 + taux)
                $prixUnitaireHT = round($prixUnitaireTTC / (1 + $tauxTVA), 2);
                $totalHT = round($totalTTC / (1 + $tauxTVA), 2);

                $montantTTC += $totalTTC;

                $detailsAvecCalculs[] = [
                    'designation' => $detail['designation'],
                    'reference' => $detail['reference'],
                    'quantite' => $quantite,
                    'prix_unitaire_ht' => $prixUnitaireHT,
                    'total_ht' => $totalHT,
                    'taux_tva' => $tauxTVA
                ];
            }

            // 5. Calculer HT et TVA totaux depuis le TTC
            $montantHT = round($montantTTC / (1 + $tauxTVA), 2);
            $montantTVA = round($montantTTC - $montantHT, 2);

            // 4. Récupérer compte contribuable de la société
            $stmt = $this->con->prepare("SELECT compte_contribuable FROM societes WHERE id_societe = ?");
            $stmt->execute([$idSociete]);
            $societe = $stmt->fetch(PDO::FETCH_ASSOC);
            $compteContribuable = $societe['compte_contribuable'] ?? null;

            // 5. Générer numéro fiscal
            $numeroFiscal = genererNumeroFiscal($compteContribuable, $idSociete);

            // 6. Générer numéro de facture classique
            $numeroFacture = $this->genererNumeroFacture($idSociete);

            // 7. Convertir montant en lettres
            $montantEnLettres = montantEnLettres($montantTTC);

            // 8. Générer QR Code local (sera remplacé par QR DGI si certification réussit)
            $qrCodeUrl = genererQRCodeFacture([
                'numero_fiscal' => $numeroFiscal,
                'date_emission' => $vente['date_heure_vente'] ?? date('Y-m-d H:i:s'),
                'montant_ttc' => $montantTTC,
                'nom_client' => $vente['raison_sociale'] ?: $vente['nom_prenom_client']
            ]);

            // 8b. Tenter la certification DGI (optionnel)
            $cachetFiscal = null;
            $signatureElectronique = null;
            $statutDGI = 'En attente';
            $dateCertificationDGI = null;

            if (class_exists('ApiDGI')) {
                $apiDGI = new ApiDGI($idSociete);
                if ($apiDGI->estActif()) {
                    $certificationDGI = $apiDGI->certifierFacture([
                        'numero_fiscal' => $numeroFiscal,
                        'montant_ttc' => $montantTTC,
                        'items' => $detailsAvecCalculs,
                        'client_name' => $vente['raison_sociale'] ?: $vente['nom_prenom_client'],
                        'client_ifu' => $vente['compte_contribuable'],
                        'client_phone' => $vente['telephone_client'],
                        'mode_paiement' => $vente['mode_paiement'] ?? 'Espèces',
                        'utilisateur_id' => $vente['utilisateur_id'],
                        'utilisateur_nom' => $_SESSION['nom_utilisateur'] ?? 'Système'
                    ]);

                    if ($certificationDGI && $certificationDGI['success']) {
                        $cachetFiscal = $certificationDGI['cachet_fiscal'];
                        $signatureElectronique = $certificationDGI['signature_electronique'];
                        $qrCodeUrl = $certificationDGI['qr_code_dgi']; // Remplacer par QR officiel
                        $statutDGI = 'Validée';
                        $dateCertificationDGI = $certificationDGI['date_certification'];
                    }
                }
            }

            // 9. Insérer la facture
            $stmt = $this->con->prepare("
                INSERT INTO factures (
                    numero_facture, numero_fiscal, titre_facture, date_facture, date_emission,
                    type_facture, code_client, montant_ht, montant_tva, montant_ttc,
                    montant_en_lettres, qr_code, cachet_fiscal, signature_electronique,
                    statut_dgi, date_certification_dgi, etat_facture,
                    id_societe, id_boutique, id_vente
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $numeroFacture,
                $numeroFiscal,
                'Facture de vente',                      // titre_facture
                date('Y-m-d'),                           // date_facture
                $vente['date_heure_vente'] ?? date('Y-m-d H:i:s'), // date_emission
                'vente',                                 // type_facture
                $vente['code_client'],                   // code_client
                $montantHT,                              // montant_ht
                $montantTVA,                             // montant_tva
                $montantTTC,                             // montant_ttc
                $montantEnLettres,                       // montant_en_lettres
                $qrCodeUrl,                              // qr_code
                $cachetFiscal,                           // cachet_fiscal (DGI)
                $signatureElectronique,                  // signature_electronique (DGI)
                $statutDGI,                              // statut_dgi
                $dateCertificationDGI,                   // date_certification_dgi
                'payée',                                 // etat_facture
                $idSociete,                              // id_societe
                $vente['id_boutique'],                   // id_boutique
                $idVente                                 // id_vente
            ]);

            $idFacture = $this->con->lastInsertId();

            // 10. Mettre à jour la vente avec montants HT/TVA/TTC
            $this->con->prepare("UPDATE ventes SET montant_ht = ?, montant_tva = ?, montant_ttc = ? WHERE id_vente = ?")
                ->execute([$montantHT, $montantTVA, $montantTTC, $idVente]);

            // Pas de commit() ici - la transaction parente (validerVente) s'en charge

            return [
                'id_facture' => $idFacture,
                'numero_facture' => $numeroFacture,
                'numero_fiscal' => $numeroFiscal,
                'success' => true
            ];

        } catch (Exception $e) {
            // Ne pas faire de rollBack ici - laisser la transaction parente gérer
            Logger::error('Erreur création facture FNE', ['error' => $e->getMessage()]);
            throw $e; // Relancer l'exception pour que validerVente() puisse la capturer
        }
    }

    /**
     * Générer un numéro de facture unique
     * Format: FACT-YYYYMMDD-XXXXX
     */
    private function genererNumeroFacture($idSociete)
    {
        $prefix = 'FACT-' . date('Ymd') . '-';
        $maxTentatives = 10;
        $tentative = 0;

        while ($tentative < $maxTentatives) {
            // Récupérer le dernier numéro
            $stmt = $this->con->prepare("
                SELECT numero_facture FROM factures
                WHERE numero_facture LIKE ? AND id_societe = ?
                ORDER BY numero_facture DESC LIMIT 1
            ");
            $stmt->execute([$prefix . '%', $idSociete]);
            $dernierNumero = $stmt->fetchColumn();

            if ($dernierNumero) {
                $sequence = intval(substr($dernierNumero, -5)) + 1;
            } else {
                $sequence = 1;
            }

            $numeroFacture = $prefix . str_pad($sequence, 5, '0', STR_PAD_LEFT);

            // Vérifier que le numéro n'existe pas déjà (double vérification)
            $stmt = $this->con->prepare("SELECT COUNT(*) FROM factures WHERE numero_facture = ?");
            $stmt->execute([$numeroFacture]);
            $existe = $stmt->fetchColumn();

            if (!$existe) {
                return $numeroFacture;
            }

            // Si le numéro existe déjà, réessayer avec +1
            $tentative++;
        }

        // Fallback : utiliser timestamp
        return $prefix . substr(time(), -5);
    }

    /**
     * Récupérer les informations complètes d'une facture pour impression FNE
     *
     * @param string $numeroFacture
     * @param int $idSociete
     * @return array|false
     */
    public function getFactureFNE($numeroFacture, $idSociete)
    {
        try {
            // 1. Récupérer facture
            $stmt = $this->con->prepare("
                SELECT f.*,
                       c.nom_prenom_client, c.adresse_client, c.telephone_client, c.email_client,
                       c.type_client, c.raison_sociale, c.compte_contribuable AS client_compte_contribuable,
                       c.registre_commerce AS client_numero_rcc,
                       s.nom_societe, s.compte_contribuable, s.numero_rcc, s.regime_imposition,
                       s.service_impots, s.adresse_complete, s.nom_banque, s.code_guichet,
                       s.numero_compte, s.cle_rib, s.code_swift, s.logo_societe,
                       b.nom_boutique
                FROM factures f
                LEFT JOIN clients c ON c.code_client = f.code_client
                LEFT JOIN societes s ON s.id_societe = f.id_societe
                LEFT JOIN boutiques b ON b.code_boutique = f.id_boutique
                WHERE (f.numero_facture = ? OR f.numero_fiscal = ?) AND f.id_societe = ?
            ");
            $stmt->execute([$numeroFacture, $numeroFacture, $idSociete]);
            $facture = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$facture) {
                return false;
            }

            // 2. Récupérer les détails de vente
            $stmt = $this->con->prepare("
                SELECT dv.*, p.designation, p.reference
                FROM details_vente dv
                LEFT JOIN produits p ON p.reference = dv.reference_produit
                WHERE dv.id_vente = ?
            ");
            $stmt->execute([$facture['id_vente']]);
            $details = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 3. Calculer les montants HT par ligne
            $detailsFormates = [];
            foreach ($details as $detail) {
                $prixUnitaireHT = $detail['prix_unitaire'];
                $totalHT = $prixUnitaireHT * $detail['quantite'];

                $detailsFormates[] = [
                    'numero' => count($detailsFormates) + 1,
                    'designation' => $detail['designation'],
                    'reference' => $detail['reference'],
                    'quantite' => $detail['quantite'],
                    'prix_unitaire_ht' => $prixUnitaireHT,
                    'total_ht' => $totalHT
                ];
            }

            $facture['details'] = $detailsFormates;

            return $facture;

        } catch (Exception $e) {
            Logger::error('Erreur récupération facture FNE', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
