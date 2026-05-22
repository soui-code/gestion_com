<?php 
// Appel des controllers
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

// Démarrer la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Gérer la déconnexion
if (!empty($_GET['deconnexion'])) {
    session_destroy();
    header('location:' . BASE_URL . '/utilisateur/connexion');
    exit;
}

// Vérifier les paramètres GET
if (!isset($_GET['c']) || !isset($_GET['a'])) {
    header('location:' . BASE_URL . '/erreur/erreur500');
    exit;
}

$controller = htmlspecialchars($_GET['c'], ENT_QUOTES, 'UTF-8');
$action = htmlspecialchars($_GET['a'], ENT_QUOTES, 'UTF-8');

// Routes publiques (pas besoin de session)
$public_routes = ['utilisateur' => ['connexion', 'deconnexion']];

// Vérifier la session pour les routes protégées
$is_public = isset($public_routes[$controller]) && in_array($action, $public_routes[$controller]);
if (!$is_public && empty($_SESSION['utilisateur_id'])) {
    header('location:' . BASE_URL . '/utilisateur/connexion');
    exit;
}

// Vérifier si la classe existe et la méthode existe
if (class_exists($controller) && method_exists($controller, $action)) {
    $variable = new $controller();
    $variable->$action();
} else {
    header('location:' . BASE_URL . '/erreur/erreur404');
    exit;
}