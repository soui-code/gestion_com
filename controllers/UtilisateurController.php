<?php
session_start();
require 'models/utilisateur.php';
require 'BaseController.php';

class utilisateur extends BaseController
{
    public $message = "";

    function connexion()
    {
        $utilisateur = new utilisateurmodel();

        if (isset($_POST['btn-connexion'])) {
            $utilisateur->login = $_POST['sai_login'];
            //Chiffrement du mdp
            $pass = sha1($_POST['sai_mdp']);
            $utilisateur->mdp = $pass;

            $result = $utilisateur->ConnexionUtilisateur();
            if (!empty($result[0])) {
                if ($result[0]['role'] == "manager" && $result[0]['etat'] == "actif") {
                    $_SESSION['utilisateur_id'] = $result[0]['utilisateur_id'];
                    $_SESSION['nom'] = $result[0]['nom'];
                    $_SESSION['login'] = $result[0]['login'];
                    $_SESSION['mdp'] = $result[0]['mdp'];
                    $_SESSION['role'] = $result[0]['role'];
                    $_SESSION['etat'] = $result[0]['etat'];
                    redirect('utilisateur/dashboard');
                }
                elseif ($result[0]['role'] == "caissier" && $result[0]['etat'] == "actif") {
                    $_SESSION['utilisateur_id'] = $result[0]['utilisateur_id'];
                    $_SESSION['nom'] = $result[0]['nom'];
                    $_SESSION['login'] = $result[0]['login'];
                    $_SESSION['mdp'] = $result[0]['mdp'];
                    $_SESSION['role'] = $result[0]['role'];
                    $_SESSION['etat'] = $result[0]['etat'];
                    redirect('utilisateur/dashboard');
                }
                else {
                    $this->message = 1;
                }
            }
            else {
                $this->message = 1;
            }
        }
        $message = $this->message;

        // include 'views/utilisateur/connexion.php';
        $this->render('utilisateur/connexion', [
            'message' => $message,
        ]);
    }

    function dashboard()
    {

        require_once './models/dashboard.php';
        $dashboardModel = new DashboardModel();

        $stats = $dashboardModel->getStats();

        $this->render('utilisateur/tableau de bord', [
            'stats' => $stats
        ]);    }

    function ventes_mensuelles()
    {


        $this->render('utilisateur/ajax/ventes_mensuelles');
    }

    function modes_paiement()
    {


        $this->render('utilisateur/ajax/modes_paiement');
    }

    function enregistrement()
    {
        $utilisateur = new utilisateurmodel();

        if (isset($_POST['btn-recherche'])) {
            $utilisateur->nom = $_POST['sai_recherche'];
            $result = $utilisateur->Recherche();
        }
        else {
            $result = $utilisateur->ListeUtilisateur();
        }


        if (!empty($_GET['supprimer'])) {
            $utilisateur->utilisateur_id = $_GET['supprimer'];
            $utilisateur->SupprimerUtilisateur();
            setFlash('success', 'Utilisateur supprimé.');
            redirect('/utilisateur/enregistrement');
        }

        if (isset($_POST['btn-ajouter'])) {
            $utilisateur->login = $_POST['sai_login'];
            $utilisateur->nom = $_POST['sai_nom'];
            $utilisateur->mdp = sha1("gestion");
            $utilisateur->role = $_POST['sai_role'];
            $utilisateur->numero = $_POST['sai_numero'];
            $utilisateur->etat = "actif";

            $utilisateur->AjoutUtilisateur();
            setFlash('success', 'Utilisateur créé avec succès !');
            redirect('/utilisateur/enregistrement');
        }

        if (isset($_POST['modifier'])) {
            $utilisateur->login = $_POST['sai_login'];
            $utilisateur->utilisateur_id = $_POST['modifier'];
            $utilisateur->nom = $_POST['sai_nom'];
            $utilisateur->role = $_POST['sai_role'];
            $utilisateur->numero = $_POST['sai_numero'];
            $utilisateur->etat = $_POST['sai_etat'];

            $utilisateur->ModificationUtilisateur();
            setFlash('success', 'Utilisateur modifié avec succès !');
            redirect('/utilisateur/enregistrement');
        }

        // include 'views/utilisateur/enregistrement-utilisateur.php';
        $this->render('utilisateur/enregistrement-utilisateur', [
            'result' => $result,
        ]);
    }
}