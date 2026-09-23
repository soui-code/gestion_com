<?php
require 'models/vente.php';
class vente extends BaseController {

    public function effectuer()
    {
        $vente = new VenteModel();
        $id_societe = getSocieteActive() ?? null;
        $id_boutique = getBoutiqueActive();
        $data = [
            'produits' => $vente->getTousProduits($id_societe, $id_boutique),
            'taxes' => $vente->getTaxes($id_societe),
            'clients' => $vente->getClients($id_societe, $id_boutique),
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
    $resultats = $model->rechercheProduit($term, getSocieteActive() ?? null, getBoutiqueActive());

    header('Content-Type: application/json');
    echo json_encode($resultats);
    exit;
}

    // Validation de la vente
    public function valider()
    {
        header('Content-Type: application/json');

        try {
            if (!isset($_POST['action']) || $_POST['action'] !== 'valider_vente') {
                echo json_encode(['success' => false, 'error' => 'Action invalide']);
                exit;
            }

            $panier = json_decode($_POST['panier'] ?? '[]', true);
            if (empty($panier)) {
                echo json_encode(['success' => false, 'error' => 'Panier vide']);
                exit;
            }

            $vente = new VenteModel();

            // Décoder les taxes (array)
            $taxes_array = json_decode($_POST['taxes_array'] ?? '[]', true);
            if (!is_array($taxes_array)) {
                $taxes_array = [];
            }

            $result = $vente->validerVente(
                $panier,
                !empty($_POST['client_id']) ? $_POST['client_id'] : null,
                $taxes_array,  // Passer l'array de taxes
                $_POST['remise'] ?? 0,
                $_POST['montant_paye'] ?? 0,
                $_POST['mode_paiement'] ?? 'Espèces',
                $_SESSION['utilisateur_id'],
                getSocieteActive() ?? null,
                getBoutiqueActive()
            );

            Logger::info('Vente validée', [
                'success' => $result['success'] ?? false,
                'facture' => $result['facture'] ?? null,
                'utilisateur_id' => $_SESSION['utilisateur_id']
            ]);

            echo json_encode($result);
        } catch (Exception $e) {
            Logger::error('Erreur validation vente', ['error' => $e->getMessage()]);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }
}
