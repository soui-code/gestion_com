<?php
require 'models/client.php';

class ClientController extends BaseController{

    public function enregistrement(){
        $client = new ClientModel();
        $result = [];

        try {
            $result = $client->ListeClient();
        } catch (Exception $e) {
            setFlash('danger', $e->getMessage());
            $result = [];
        }

        if (isset($_POST['btn-ajouter-client'])) {
            try {
                $nom = $_POST['sai_nom_prenom_client'] ?? '';
                $telephone = $_POST['sai_telephone_client'] ?? '';
                $email = $_POST['sai_email_client'] ?? '';

                if (empty($nom) || empty($telephone)) {
                    setFlash('danger', 'Le nom et téléphone sont obligatoires');
                } else {
                    $client->nom_prenom_client = $nom;
                    $client->telephone_client = $telephone;
                    $client->email_client = $email ?? '';
                    $client->adresse_client = $_POST['sai_adresse_client'] ?? '';
                    $client->etat_client = $_POST['sai_etat_client'] ?? 'actif';

                    $client->AjoutClient();
                    setFlash('success', 'Client ajouté avec succès !');
                }
            } catch (Exception $e) {
                setFlash('danger', $e->getMessage());
            }
            redirect('/client/enregistrement');
        }

        if (isset($_POST['btn-modifier-client'])) {
            try {
                $code = (int)($_POST['sai_code_client'] ?? 0);
                $nom = $_POST['sai_nom_prenom_client'] ?? '';
                $telephone = $_POST['sai_telephone_client'] ?? '';

                if ($code <= 0 || empty($nom) || empty($telephone)) {
                    setFlash('danger', 'Tous les champs obligatoires doivent être remplis');
                } else {
                    $client->code_client = $code;
                    $client->nom_prenom_client = $nom;
                    $client->telephone_client = $telephone;
                    $client->email_client = $_POST['sai_email_client'] ?? '';
                    $client->adresse_client = $_POST['sai_adresse_client'] ?? '';
                    $client->etat_client = $_POST['sai_etat_client'] ?? 'actif';

                    $client->ModifierClient();
                    setFlash('success', 'Client modifié avec succès !');
                }
            } catch (Exception $e) {
                setFlash('danger', $e->getMessage());
            }
            redirect('/client/enregistrement');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn-supprimer-client'])) {
            try {
                $code = (int)($_POST['sai_code_client'] ?? 0);
                if ($code <= 0) {
                    setFlash('danger', 'Code client invalide');
                } else {
                    $client->code_client = $code;
                    $client->SupprimerClient();
                    setFlash('success', 'Client supprimé avec succès !');
                }
            } catch (Exception $e) {
                setFlash('danger', $e->getMessage());
            }
            redirect('/client/enregistrement');
        }

        $this->render('client/enregistrement-client',[
            'result' => $result,
        ]);
    }
}

// Alias pour compatibilité avec le router
class_alias('ClientController', 'client');