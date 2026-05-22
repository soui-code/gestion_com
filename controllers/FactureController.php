<?php
require 'models/facture.php';

class facture extends BaseController
{
    public function liste()
    {
        $facture = new facturemodel();

        $result = $facture->ListeFacture();
        $this->render('facture/liste-facture', [
            'result' => $result,
        ]);
    }






    
public function imprimer($id = null)
    {

        $id = $_GET['id'];

        if (!$id) {
            die("ID invalide");
        }

        // --- Appel du modèle ---
        $model = new facturemodel();
        $facture = $model->getFactureAvecClient($id);

        if (!$facture) {
            die("Facture introuvable");
        }

        // --- Récupérer les articles (commandes) liées ---
        // Le numéro de facture peut être au format 'FACT-YYYYMMDD-ID' ou juste 'ID'
        $itemsQuery = "FACT-" . date('Ymd', strtotime($facture['date_facture'])) . "-" . str_pad($facture['numero_facture'], 4, '0', STR_PAD_LEFT);
        $items = $model->getFactureItems($itemsQuery);
        
        if (empty($items)) {
            // Tentative par ID simple
            $items = $model->getFactureItems((string)$facture['numero_facture']);
        }
        
        if (empty($items)) {
            // Tentative par Titre (pour les anciennes factures)
            $items = $model->getFactureItems($facture['titre_facture']);
        }

        $taxe = null;
        if (!empty($facture['code_taxe'])) {
            $con = getDatabaseConnection();
            $taxeStmt = $con->prepare("SELECT * FROM taxes WHERE code_taxe = ?");
            $taxeStmt->execute([$facture['code_taxe']]);
            $taxe = $taxeStmt->fetch(PDO::FETCH_ASSOC);
        }

        $this->render('facture/imprimer', [
            'facture' => $facture,
            'items' => $items,
            'taxe' => $taxe
        ]);
        exit;
    }
}