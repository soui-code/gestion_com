<?php
session_start();
require 'models/utilisateur.php';
require 'BaseController.php';

class UtilisateurController extends BaseController
{
    public $message = "";

    function connexion()
    {
        $utilisateur = new UtilisateurModel();

        if (isset($_POST['btn-connexion'])) {
            $login = $_POST['sai_login'] ?? '';
            $mdp = $_POST['sai_mdp'] ?? '';

            if (empty($login) || empty($mdp)) {
                $this->message = "Login et mot de passe sont obligatoires";
                $this->render('utilisateur/connexion', ['message' => $this->message]);
                return;
            }

            try {
                $result = $utilisateur->ConnexionUtilisateur($login);
                
                if (!empty($result) && password_verify($mdp, $result['mdp']) && $result['etat'] == 'actif') {
                    if (in_array($result['role'], ['manager', 'caissier'])) {
                        // Stocker UNIQUEMENT les infos essentielles (pas de mot de passe!)
                        $_SESSION['utilisateur_id'] = $result['utilisateur_id'];
                        $_SESSION['nom'] = $result['nom'];
                        $_SESSION['login'] = $result['login'];
                        $_SESSION['role'] = $result['role'];
                        $_SESSION['etat'] = $result['etat'];
                        
                        redirect('utilisateur/dashboard');
                    } else {
                        $this->message = "Rôle non autorisé";
                    }
                } else {
                    $this->message = "Login ou mot de passe incorrect";
                }
            } catch (Exception $e) {
                $this->message = "Erreur de connexion : " . $e->getMessage();
            }
        }

        $message = $this->message;
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
        ]);
    }

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
        $utilisateur = new UtilisateurModel();
        $result = [];

        if (isset($_POST['btn-recherche'])) {
            $nom = $_POST['sai_recherche'] ?? '';
            try {
                $result = $utilisateur->Recherche($nom);
                if (empty($result)) {
                    setFlash('info', 'Aucun utilisateur trouvé avec ce nom');
                }
            } catch (Exception $e) {
                setFlash('danger', $e->getMessage());
            }
        } else {
            try {
                $result = $utilisateur->ListeUtilisateur();
            } catch (Exception $e) {
                setFlash('danger', $e->getMessage());
                $result = [];
            }
        }

        // Suppression sécurisée (POST + vérification)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn-supprimer'])) {
            if ($_SESSION['role'] !== 'manager') {
                setFlash('danger', 'Non autorisé: seul un manager peut supprimer des utilisateurs');
            } else {
                $id = (int)($_POST['id_utilisateur'] ?? 0);
                if ($id > 0) {
                    try {
                        $utilisateur->utilisateur_id = $id;
                        $utilisateur->SupprimerUtilisateur();
                        setFlash('success', 'Utilisateur supprimé avec succès');
                    } catch (Exception $e) {
                        setFlash('danger', $e->getMessage());
                    }
                }
            }
            redirect('/utilisateur/enregistrement');
        }

        // Ajout d'utilisateur
        if (isset($_POST['btn-ajouter'])) {
            if ($_SESSION['role'] !== 'manager') {
                setFlash('danger', 'Non autorisé: seul un manager peut ajouter des utilisateurs');
            } else {
                $login = $_POST['sai_login'] ?? '';
                $nom = $_POST['sai_nom'] ?? '';
                $role = $_POST['sai_role'] ?? '';
                $numero = $_POST['sai_numero'] ?? '';

                if (empty($login) || empty($nom) || empty($role)) {
                    setFlash('danger', 'Tous les champs sont obligatoires');
                } else {
                    try {
                        $utilisateur->login = $login;
                        $utilisateur->nom = $nom;
                        // Générer un mot de passe par défaut sécurisé
                        $mdp_temp = bin2hex(random_bytes(4)); // Ex: "a1b2c3d4"
                        $utilisateur->mdp = password_hash('gestion_' . $mdp_temp, PASSWORD_BCRYPT);
                        $utilisateur->role = $role;
                        $utilisateur->numero = $numero;
                        $utilisateur->etat = "actif";

                        $utilisateur->AjoutUtilisateur();
                        setFlash('success', 'Utilisateur créé avec succès ! (Mot de passe par défaut: gestion_' . $mdp_temp . ')');
                    } catch (Exception $e) {
                        setFlash('danger', $e->getMessage());
                    }
                }
            }
            redirect('/utilisateur/enregistrement');
        }

        // Modification d'utilisateur
        if (isset($_POST['btn-modifier'])) {
            if ($_SESSION['role'] !== 'manager') {
                setFlash('danger', 'Non autorisé: seul un manager peut modifier des utilisateurs');
            } else {
                $id = (int)($_POST['id_utilisateur'] ?? 0);
                $login = $_POST['sai_login'] ?? '';
                $nom = $_POST['sai_nom'] ?? '';
                $role = $_POST['sai_role'] ?? '';
                $numero = $_POST['sai_numero'] ?? '';
                $etat = $_POST['sai_etat'] ?? 'actif';

                if (empty($login) || empty($nom) || empty($role) || $id <= 0) {
                    setFlash('danger', 'Tous les champs sont obligatoires');
                } else {
                    try {
                        $utilisateur->utilisateur_id = $id;
                        $utilisateur->login = $login;
                        $utilisateur->nom = $nom;
                        $utilisateur->role = $role;
                        $utilisateur->numero = $numero;
                        $utilisateur->etat = $etat;

                        $utilisateur->ModificationUtilisateur();
                        setFlash('success', 'Utilisateur modifié avec succès');
                    } catch (Exception $e) {
                        setFlash('danger', $e->getMessage());
                    }
                }
            }
            redirect('/utilisateur/enregistrement');
        }

        $this->render('utilisateur/enregistrement-utilisateur', [
            'result' => $result,
        ]);
    }
}

// Alias pour compatibilité avec le router
class_alias('UtilisateurController', 'utilisateur');