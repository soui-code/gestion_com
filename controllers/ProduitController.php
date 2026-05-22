<?php
require_once 'models/produit.php';

class ProduitController extends BaseController{

    function liste(){
        $produit = new ProduitModel();

        try {
            $result = $produit->ListeProduit();
        } catch (Exception $e) {
            setFlash('danger', $e->getMessage());
            $result = [];
        }

        require_once 'models/categorie.php';
        require_once 'models/fournisseur.php';
        $catModel = new CategorieModel();
        $fournModel = new FournisseurModel();
        
        try {
            $categories = $catModel->getActiveCategories();
            $fournisseurs = $fournModel->getActiveFournisseurs();
        } catch (Exception $e) {
            $categories = [];
            $fournisseurs = [];
        }

        if (isset($_POST['btn-ajouter'])) {
            try {
                $prix_achat = (float)$_POST['sai_prix_achat'];
                $prix_vente = (float)$_POST['sai_prix_vente'];

                // Validation des prix
                if ($prix_achat < 0 || $prix_vente < 0) {
                    setFlash('danger', 'Les prix ne peuvent pas être négatifs');
                } elseif ($prix_vente < $prix_achat) {
                    setFlash('warning', 'Attention: Le prix de vente est inférieur au prix d\'achat');
                }

                $produit->reference = $_POST['sai_reference'] ?? '';
                $produit->designation = trim($_POST['sai_designation']);
                $produit->id_categorie = !empty($_POST['sai_categorie']) ? (int)$_POST['sai_categorie'] : null;
                $produit->id_fournisseur = !empty($_POST['sai_fournisseur']) ? (int)$_POST['sai_fournisseur'] : null;
                $produit->prix_achat = $prix_achat;
                $produit->prix_vente = $prix_vente;
                $produit->stock = max(0, (int)$_POST['sai_stock']);
                $produit->stock_alert = max(0, (int)$_POST['sai_stock_alert']);
                $produit->description = trim($_POST['sai_description']);
                $produit->etat = $_POST['sai_etat'];
                $produit->utilisateur_id = $_SESSION['utilisateur_id'] ?? null;

                if (!empty($_FILES['sai_photo']['name'])) {
                    $produit->photo_type=$_FILES['sai_photo']['type'];
                    $produit->photo=file_get_contents($_FILES['sai_photo']['tmp_name']);   
                } else {
                    $produit->photo_type="";
                    $produit->photo="";
                }
                
                $produit->AjouterProduit();
                setFlash('success', 'Produit ajouté avec succès !');
                redirect('/produit/liste');
            } catch (Exception $e) {
                setFlash('danger', $e->getMessage());
            }
        }

        if (isset($_POST['btn-modifier'])) {
            try {
                $prix_achat = (float)$_POST['sai_prix_achat'];
                $prix_vente = (float)$_POST['sai_prix_vente'];

                if ($prix_achat < 0 || $prix_vente < 0) {
                    setFlash('danger', 'Les prix ne peuvent pas être négatifs');
                } elseif ($prix_vente < $prix_achat) {
                    setFlash('warning', 'Attention: Le prix de vente est inférieur au prix d\'achat');
                }

                $produit->reference = $_POST['sai_reference'] ?? '';
                $produit->designation = trim($_POST['sai_designation']);
                $produit->id_categorie = !empty($_POST['sai_categorie']) ? (int)$_POST['sai_categorie'] : null;
                $produit->id_fournisseur = !empty($_POST['sai_fournisseur']) ? (int)$_POST['sai_fournisseur'] : null;
                $produit->prix_achat = $prix_achat;
                $produit->prix_vente = $prix_vente;
                $produit->stock_alert = max(0, (int)$_POST['sai_stock_alert']);
                $produit->description = trim($_POST['sai_description']);
                $produit->etat = $_POST['sai_etat'];
                
                if (!empty($_FILES['sai_photo']['name'])) {
                    $produit->photo_type=$_FILES['sai_photo']['type'];
                    $produit->photo=file_get_contents($_FILES['sai_photo']['tmp_name']);   
                } else {
                    $produit->photo_type="";
                    $produit->photo="";
                }
                $produit->ModifierProduit();
                setFlash('success', 'Produit modifié avec succès !');
                redirect('/produit/liste');
            } catch (Exception $e) {
                setFlash('danger', $e->getMessage());
            }
        }

        if (isset($_POST['btn-supprimer'])) {
            try {
                $produit->reference=$_POST['sai_reference'];
                $produit->SupprimerProduit();
                setFlash('success', 'Produit supprimé avec succès !');
            } catch (Exception $e) {
                setFlash('danger', $e->getMessage());
            }
            redirect('/produit/liste');
        }

        if (isset($_POST['btn-approvisionner'])) {
            try {
                $qte = (int)$_POST['sai_quantite'];
                if ($qte <= 0) {
                    setFlash('danger', 'Quantité invalide');
                } else {
                    $produit->stock = $qte;
                    $produit->reference = $_POST['sai_reference'];
                    $produit->utilisateur_id = $_SESSION['utilisateur_id'] ?? null;
                    $produit->ApprovisionnerProduit();
                    setFlash('success', 'Stock approvisionné avec succès !');
                }
            } catch (Exception $e) {
                setFlash('danger', $e->getMessage());
            }
            redirect('/produit/liste');
        }

        $this->render('produit/enregistrement-produit',[
            'result' => $result,
            'categories' => $categories,
            'fournisseurs' => $fournisseurs
        ]);
    }
}

// Alias pour compatibilité avec le router
class_alias('ProduitController', 'produit');