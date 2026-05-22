<?php 
//Appel des controllers
require 'config/config.php';
require 'database/database.php';
require 'controllers/UtilisateurController.php';
require 'controllers/ErreurController.php';
require 'controllers/ProduitController.php';
require 'controllers/FactureController.php';
require 'controllers/ClientController.php';
require 'controllers/CommandeController.php';
require 'controllers/TransactionController.php';
require 'controllers/TaxeController.php';
require 'controllers/BoutiqueController.php';
require 'controllers/VenteController.php';
require 'controllers/CategorieController.php';
require 'controllers/FournisseurController.php';
require 'controllers/MouvementController.php';


if (!empty($_GET['deconnexion'])) {
            session_destroy();
            header('location:' . BASE_URL . '/utilisateur/connexion');
        }

        
if ($_GET['c'] &&  $_GET['a']) {
    $controller = $_GET['c'];
    $action = $_GET['a'];
    
    if (class_exists($controller) && method_exists($controller,$action) ) {
        $variable = new $controller;
        $variable->$action();
    } else {
        
        header('location:' . BASE_URL . '/erreur/erreur404');
    }
} else {
    header('location:' . BASE_URL . '/erreur/erreur500');
}