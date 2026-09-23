<?php
require 'models/taxe.php';

class taxe extends BaseController{
    public function enregistrement(){
        $taxe = new taxemodel();
        $taxe->id_societe = getSocieteActive() ?? null;
        $result=$taxe->ListeTaxe();

        if (isset($_POST['btn-ajouter-taxe'])) {
            $taxe->titre_taxe=$_POST['sai_titre_taxe'];
            $taxe->taux_taxe=$_POST['sai_taux_taxe'];
            $taxe->action_taxe=$_POST['sai_action_taxe'];
            $taxe->etat_taxe=$_POST['sai_etat_taxe'];

            $taxe->AjoutTaxe();
            setFlash('success', 'Taxe ajoutée avec succès !');
            redirect('/taxe/enregistrement');
        }

        if (isset($_POST['btn-modifier-taxe'])) {
            $taxe->titre_taxe=$_POST['sai_titre_taxe'];
            $taxe->code_taxe=$_POST['sai_code_taxe'];
            $taxe->taux_taxe=$_POST['sai_taux_taxe'];
            $taxe->action_taxe=$_POST['sai_action_taxe'];
            $taxe->etat_taxe=$_POST['sai_etat_taxe'];

           $taxe->ModificationTaxe();
           setFlash('success', 'Taxe modifiée avec succès !');
            redirect('/taxe/enregistrement');
        }

        if (isset($_POST['btn-supprimer-taxe'])) {
            $taxe->code_taxe=$_POST['sai_code_taxe'];

            $taxe->SuppressionTaxe();
            setFlash('success', 'Taxe supprimée avec succès !');
            redirect('/taxe/enregistrement');
        }

        $this->render('taxe/enregistrement-taxe',[
            'result' => $result,
        ]);
    }
}