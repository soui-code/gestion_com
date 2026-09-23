<?php
// Bootstrap de l'application (charge tout le nécessaire)
require_once __DIR__ . '/bootstrap.php';

// Appel des controllers
require_once __DIR__ . '/controllers/UtilisateurController.php';
require_once __DIR__ . '/controllers/ErreurController.php';
require_once __DIR__ . '/controllers/ProduitController.php';
require_once __DIR__ . '/controllers/FactureController.php';
require_once __DIR__ . '/controllers/ClientController.php';
require_once __DIR__ . '/controllers/CommandeController.php';
require_once __DIR__ . '/controllers/TransactionController.php';
require_once __DIR__ . '/controllers/TaxeController.php';
require_once __DIR__ . '/controllers/BoutiqueController.php';
require_once __DIR__ . '/controllers/VenteController.php';
require_once __DIR__ . '/controllers/CategorieController.php';
require_once __DIR__ . '/controllers/FournisseurController.php';
require_once __DIR__ . '/controllers/MouvementController.php';
require_once __DIR__ . '/controllers/PeremptionController.php';
require_once __DIR__ . '/controllers/SocieteController.php';
require_once __DIR__ . '/controllers/plan.php';
require_once __DIR__ . '/controllers/abonnement.php';

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
    Logger::info('Tentative d\'accès non autorisé', [
        'controller' => $controller,
        'action' => $action,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ]);
    header('location:' . BASE_URL . '/utilisateur/connexion');
    exit;
}

// Vérification CSRF pour les requêtes POST (sauf connexion et API AJAX)
$csrf_exceptions = [
    'utilisateur' => ['connexion', 'ventes_mensuelles', 'modes_paiement'],
    'vente' => ['valider', 'recherche'],
    'boutique' => ['transferer', 'produits'],
    'plan' => [],
    'abonnement' => []
];
$skip_csrf = isset($csrf_exceptions[$controller]) && in_array($action, $csrf_exceptions[$controller]);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$skip_csrf) {
    CSRF::verifyRequest();
}

// Vérifier si la classe existe et la méthode existe
if (class_exists($controller) && method_exists($controller, $action)) {
    $variable = new $controller();
    $variable->$action();
} else {
    header('location:' . BASE_URL . '/erreur/erreur404');
    exit;
}

