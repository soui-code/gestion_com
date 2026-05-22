<?php
require 'models/client.php';

class client extends BaseController{

    public function enregistrement(){
        $client=new clientmodel();

        if (isset($_POST['btn-ajouter-client'])) {
            $client->nom_prenom_client=$_POST['sai_nom_prenom_client'];
            $client->telephone_client=$_POST['sai_telephone_client'];
            $client->email_client=$_POST['sai_email_client'];
            $client->adresse_client=$_POST['sai_adresse_client'];
            $client->etat_client=$_POST['sai_etat_client'];

            $client->AjoutClient();
            setFlash('success', 'Client ajouté avec succès !');
            redirect('/client/enregistrement');
        }

        if (isset($_POST['btn-modifier-client'])) {
            $client->nom_prenom_client=$_POST['sai_nom_prenom_client'];
            $client->code_client=$_POST['sai_code_client'];
            $client->telephone_client=$_POST['sai_telephone_client'];
            $client->email_client=$_POST['sai_email_client'];
            $client->adresse_client=$_POST['sai_adresse_client'];
            $client->etat_client=$_POST['sai_etat_client'];

            $client->ModifierClient();
            setFlash('success', 'Client modifié avec succès !');
            redirect('/client/enregistrement');
        }

        if (isset($_POST['btn-supprimer-client'])) {
            $client->code_client=$_POST['sai_code_client'];

            $client->SupprimerClient();
            setFlash('success', 'Client supprimé avec succès !');
            redirect('/client/enregistrement');
        }

        $result = $client->ListeClient();
        $this->render('client/enregistrement-client',[
            'result' => $result,
        ]);
    }
}