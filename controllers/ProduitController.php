<?php
require 'models/produit.php';

class produit extends BaseController{


    function liste(){
        $produit = new produitmodel();
        $result = $produit -> ListeProduit();

        require_once 'models/categorie.php';
        require_once 'models/fournisseur.php';
        $catModel = new categoriemodel();
        $fournModel = new fournisseurmodel();
        $categories = $catModel->getActiveCategories();
        $fournisseurs = $fournModel->getActiveFournisseurs();

        if (isset($_POST['btn-ajouter'])) {
            $produit->reference = $_POST['sai_reference'];
            $produit->designation = trim($_POST['sai_designation']);
            $produit->id_categorie = !empty($_POST['sai_categorie']) ? (int)$_POST['sai_categorie'] : null;
            $produit->id_fournisseur = !empty($_POST['sai_fournisseur']) ? (int)$_POST['sai_fournisseur'] : null;
            $produit->prix_achat = max(0, (float)$_POST['sai_prix_achat']);
            $produit->prix_vente = max(0, (float)$_POST['sai_prix_vente']);
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
        }

        if (isset($_POST['btn-modifier'])) {
            $produit->reference = $_POST['sai_reference'];
            $produit->designation = trim($_POST['sai_designation']);
            $produit->id_categorie = !empty($_POST['sai_categorie']) ? (int)$_POST['sai_categorie'] : null;
            $produit->id_fournisseur = !empty($_POST['sai_fournisseur']) ? (int)$_POST['sai_fournisseur'] : null;
            $produit->prix_achat = max(0, (float)$_POST['sai_prix_achat']);
            $produit->prix_vente = max(0, (float)$_POST['sai_prix_vente']);
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
        }


        if (isset($_POST['btn-supprimer'])) {
            $produit->reference=$_POST['sai_reference'];
            $produit->SupprimerProduit();
            setFlash('success', 'Produit supprimé avec succès !');
            redirect('/produit/liste');
        }

        if (isset($_POST['btn-approvisionner'])) {
            $qte = (int)$_POST['sai_quantite'];
            if ($qte > 0) {
                $produit->stock = $qte;
                $produit->reference = $_POST['sai_reference'];
                $produit->utilisateur_id = $_SESSION['utilisateur_id'] ?? null;
                $produit->ApprovisionnerProduit();
                setFlash('success', 'Stock approvisionné avec succès !');
            } else {
                setFlash('error', 'Quantité invalide.');
            }
            redirect('/produit/liste');
        }

        // include 'views/produit/enregistrement-produit.php';
        $this->render('produit/enregistrement-produit',[
            'result' => $result,
            'categories' => $categories,
            'fournisseurs' => $fournisseurs
        ]);
    }
}