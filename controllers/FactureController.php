<?php
require 'models/facture.php';
require 'models/facture_fne.php';

class facture extends BaseController
{
    public function liste()
    {
        $facture = new facturemodel();
        $facture->id_societe = getSocieteActive() ?? null;
        $facture->id_boutique = getBoutiqueActive() ?? null;

        $result = $facture->ListeFacture();
        $this->render('facture/liste-facture', [
            'result' => $result,
        ]);
    }






    
    /**
     * Ancienne méthode d'impression - Redirige vers FNE
     * @deprecated Utiliser imprimer_fne() à la place
     */
    public function imprimer($id = null)
    {
        $id = $_GET['id'] ?? $_GET['numero'] ?? $id;

        if (!$id) {
            die("ID invalide");
        }

        // Rediriger vers la nouvelle impression FNE
        redirect('/facture/imprimer_fne?id=' . urlencode($id));
    }

    /**
     * Imprimer la Facture Normalisée Électronique (FNE)
     */
    public function imprimer_fne($id = null)
    {
        $id = $_GET['id'] ?? $_GET['numero'] ?? $id;

        if (!$id) {
            die("Numéro de facture invalide");
        }

        $factureFNE = new FactureFNE();
        $facture = $factureFNE->getFactureFNE($id, getSocieteActive() ?? null);

        if (!$facture) {
            die("Facture FNE introuvable : " . htmlspecialchars($id));
        }

        // Charger la vue FNE
        require 'views/facture/imprimer_fne.php';
        exit;
    }
}