<?php
require 'models/vente.php';
class vente extends BaseController {

    public function effectuer()
    {
        $vente = new VenteModel();
        $data = [
            'produits' => $vente->getTousProduits(),
            'taxes' => $vente->getTaxes(),
            'clients' => $vente->getClients(),
            'utilisateur_id' => $_SESSION['utilisateur_id']
        ];

        $this->render('vente/effectuer', $data);
    }

    // Dans VenteController.php
public function recherche()
{
    if (!isset($_GET['term'])) {
        echo json_encode([]);
        exit;
    }

    $term = trim($_GET['term']);
    if (strlen($term) < 2) {
        echo json_encode([]);
        exit;
    }

    require_once './models/vente.php';
    $model = new VenteModel();
    $resultats = $model->rechercheProduit($term);

    header('Content-Type: application/json');
    echo json_encode($resultats);
    exit;
}

    // Validation de la vente
    public function valider()
    {
        $vente = new VenteModel();
        if ($_POST['action'] === 'valider_vente') {
            $panier = json_decode($_POST['panier'], true);
            $result = $vente->validerVente(
                $panier,
                $_POST['client_id'] ?: null,
                $_POST['taxe_code'],
                $_POST['remise'] ?? 0,
                $_POST['montant_paye'],
                $_POST['mode_paiement'],
                $_SESSION['utilisateur_id']
            );

            echo json_encode($result);
            exit;
        }
    }
}