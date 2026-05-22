<?php
require 'models/boutique.php';

class boutique extends BaseController{

    public function enregistrement(){
        $boutique=new boutiquemodel();
        $result = $boutique->Listeboutique();

        if (isset($_POST['btn-ajouter-boutique'])) {

            $boutique->nom_boutique=$_POST['sai_nom_boutique'];
            $boutique->telephone_boutique=$_POST['sai_telephone_boutique'];
            $boutique->latitude=$_POST['sai_latitude'];
            $boutique->longitude=$_POST['sai_longitude'];
            $boutique->produit_json=$_POST['sai_produit_json'];
            $boutique->etat_boutique=$_POST['sai_etat_boutique'];

            $boutique->AjoutBoutique();
            setFlash('success', 'Boutique ajoutée avec succès !');
            redirect('/boutique/enregistrement');
        }

        
        if (isset($_POST['btn-modifier-boutique'])) {
            $boutique->nom_boutique=$_POST['sai_nom_boutique'];
            $boutique->telephone_boutique=$_POST['sai_telephone_boutique'];
            $boutique->latitude=$_POST['sai_latitude'];
            $boutique->longitude=$_POST['sai_longitude'];
            $boutique->produit_json=$_POST['sai_produit_json'];
            $boutique->etat_boutique=$_POST['sai_etat_boutique'];

            $boutique->code_boutique=$_POST['sai_code_boutique'];
            
            $boutique->ModificationBoutique();
            setFlash('success', 'Boutique modifiée avec succès !');
            redirect('/boutique/enregistrement');
        }

        if (isset($_POST['btn-supprimer-boutique'])) {

            $boutique->code_boutique=$_POST['sai_code_boutique'];
            $boutique->SuppressionBoutique();
            setFlash('success', 'Boutique supprimée avec succès !');
            redirect('/boutique/enregistrement');
        }


        $this->render('boutique/enregistrement-boutique',[
            'result' => $result,
        ]);
    }

        public function Map(){
            $boutique = new boutiquemodel();
            $boutiques = $boutique->getAllBoutiques();

        $this->render('boutique/map-boutique',[
            'boutiques' => $boutiques,
        ]);
    }
}