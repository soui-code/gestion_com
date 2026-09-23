<?php
require 'models/categorie.php';

class categorie extends BaseController {
    
    public function liste() {
        $cat = new categoriemodel();
        $cat->id_societe = getSocieteActive() ?? null;
        $cat->id_boutique = getBoutiqueActive() ?? null;
        
        if (isset($_POST['btn-ajouter'])) {
            $cat->nom_categorie = trim($_POST['sai_nom']);
            $cat->description = trim($_POST['sai_description']);
            $cat->etat = $_POST['sai_etat'];
            
            if($cat->AjouterCategorie()) {
                setFlash('success', 'Catégorie ajoutée avec succès.');
            } else {
                setFlash('danger', 'Erreur lors de l\'ajout.');
            }
            redirect('/categorie/liste');
        }

        if (isset($_POST['btn-modifier'])) {
            $cat->id_categorie = $_POST['sai_id'];
            $cat->nom_categorie = trim($_POST['sai_nom']);
            $cat->description = trim($_POST['sai_description']);
            $cat->etat = $_POST['sai_etat'];
            
            if($cat->ModifierCategorie()) {
                setFlash('success', 'Catégorie modifiée avec succès.');
            } else {
                setFlash('danger', 'Erreur lors de la modification.');
            }
             redirect('/categorie/liste');
        }

        if (isset($_POST['btn-supprimer'])) {
            $cat->id_categorie = $_POST['sai_id'];
            if($cat->SupprimerCategorie()) {
                 setFlash('success', 'Catégorie supprimée avec succès.');
            } else {
                 setFlash('danger', 'Erreur: Cette catégorie est peut-être liée à des produits.');
            }
            redirect('/categorie/liste');
        }

        $result = $cat->ListeCategories();
        $this->render('categorie/liste', [
            'categories' => $result
        ]);
    }
}
?>
