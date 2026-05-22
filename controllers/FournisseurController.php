<?php
require 'models/fournisseur.php';

class fournisseur extends BaseController {
    
    public function liste() {
        $fr = new fournisseurmodel();
        
        if (isset($_POST['btn-ajouter'])) {
            $fr->nom_fournisseur = trim($_POST['sai_nom']);
            $fr->telephone = trim($_POST['sai_tel']);
            $fr->email = trim($_POST['sai_email']);
            $fr->adresse = trim($_POST['sai_adresse']);
            $fr->etat = $_POST['sai_etat'];
            
            if($fr->AjouterFournisseur()) {
                setFlash('success', 'Fournisseur ajouté avec succès.');
            } else {
                setFlash('danger', 'Erreur lors de l\'ajout.');
            }
            redirect('/fournisseur/liste');
        }

        if (isset($_POST['btn-modifier'])) {
            $fr->id_fournisseur = $_POST['sai_id'];
            $fr->nom_fournisseur = trim($_POST['sai_nom']);
            $fr->telephone = trim($_POST['sai_tel']);
            $fr->email = trim($_POST['sai_email']);
            $fr->adresse = trim($_POST['sai_adresse']);
            $fr->etat = $_POST['sai_etat'];
            
            if($fr->ModifierFournisseur()) {
                setFlash('success', 'Fournisseur modifié avec succès.');
            } else {
                setFlash('danger', 'Erreur lors de la modification.');
            }
             redirect('/fournisseur/liste');
        }

        if (isset($_POST['btn-supprimer'])) {
            $fr->id_fournisseur = $_POST['sai_id'];
            if($fr->SupprimerFournisseur()) {
                 setFlash('success', 'Fournisseur supprimé avec succès.');
            } else {
                 setFlash('danger', 'Erreur: Ce fournisseur est peut-être lié à des produits.');
            }
            redirect('/fournisseur/liste');
        }

        $result = $fr->ListeFournisseurs();
        $this->render('fournisseur/liste', [
            'fournisseurs' => $result
        ]);
    }
}
?>
