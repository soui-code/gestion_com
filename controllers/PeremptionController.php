<?php
require 'models/peremption.php';

class PeremptionController extends BaseController {

    public function liste()
    {
        $peremption = new PeremptionModel();
        $peremption->id_societe = getSocieteActive() ?? null;
        $peremption->id_boutique = getBoutiqueActive() ?? null;

        if (isset($_POST['btn-ajouter'])) {
            $peremption->id_article = $_POST['id_article'] ?? 0;
            $peremption->date_fabrication = $_POST['date_fabrication'] ?? null;
            $peremption->date_peremption = $_POST['date_peremption'] ?? null;
            $peremption->nombre = $_POST['nombre'] ?? 0;

            if (empty($peremption->id_article) || empty($peremption->date_peremption)) {
                setFlash('danger', 'Article et date de péremption sont obligatoires');
            } else {
                try {
                    $peremption->Ajouter();
                    setFlash('success', 'Péremption ajoutée avec succès');
                } catch (Exception $e) {
                    setFlash('danger', $e->getMessage());
                }
            }
            redirect('/peremption/liste');
        }

        if (isset($_POST['btn-modifier'])) {
            $peremption->id = $_POST['id'] ?? 0;
            $peremption->id_article = $_POST['id_article'] ?? 0;
            $peremption->date_fabrication = $_POST['date_fabrication'] ?? null;
            $peremption->date_peremption = $_POST['date_peremption'] ?? null;
            $peremption->nombre = $_POST['nombre'] ?? 0;

            if ($peremption->id <= 0 || empty($peremption->id_article)) {
                setFlash('danger', 'Données invalides');
            } else {
                try {
                    $peremption->Modifier();
                    setFlash('success', 'Péremption modifiée avec succès');
                } catch (Exception $e) {
                    setFlash('danger', $e->getMessage());
                }
            }
            redirect('/peremption/liste');
        }

        if (isset($_POST['btn-supprimer'])) {
            $peremption->id = $_POST['id'] ?? 0;
            if ($peremption->id > 0) {
                try {
                    $peremption->Supprimer();
                    setFlash('success', 'Péremption supprimée');
                } catch (Exception $e) {
                    setFlash('danger', $e->getMessage());
                }
            }
            redirect('/peremption/liste');
        }

        $result = $peremption->Lister();
        $produits = $this->getProduits();

        $this->render('peremption/liste', [
            'result' => $result,
            'produits' => $produits,
        ]);
    }

    private function getProduits()
    {
        try {
            $con = getDatabaseConnection();
            $stmt = $con->prepare("SELECT reference, designation FROM produits WHERE etat = 'actif' AND id_societe = :id_societe ORDER BY designation");
            $stmt->execute([':id_societe' => getSocieteActive() ?? null]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
}

class_alias('PeremptionController', 'peremption');
