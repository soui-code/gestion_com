<?php
/**
 * Bootstrap de l'application
 * Charge tous les composants nécessaires dans le bon ordre
 */

// 1. Charger la configuration des variables d'environnement
require_once __DIR__ . '/config/env.php';

// 2. Charger les variables d'environnement
try {
    Env::load(__DIR__ . '/.env');
} catch (Exception $e) {
    die('ERREUR FATALE : Fichier .env introuvable. Veuillez copier .env.example vers .env et le configurer.');
}

// 3. Charger les utilitaires de base
require_once __DIR__ . '/config/logger.php';
require_once __DIR__ . '/config/helpers.php';
require_once __DIR__ . '/config/csrf.php';
require_once __DIR__ . '/config/validator.php';
require_once __DIR__ . '/config/paginator.php';

// 4. Charger la configuration principale
require_once __DIR__ . '/config/config.php';

// 5. Charger la connexion base de données
require_once __DIR__ . '/database/database.php';

// 6. Démarrer la session si ce n'est pas déjà fait
if (session_status() === PHP_SESSION_NONE) {
    $sessionLifetime = (int)Env::get('SESSION_LIFETIME', 7200);
    ini_set('session.gc_maxlifetime', $sessionLifetime);
    ini_set('session.cookie_httponly', 1);

    if (Env::get('APP_ENV') === 'production') {
        ini_set('session.cookie_secure', 1);
        ini_set('session.cookie_samesite', 'Strict');
    }

    session_start();
}

// 7. Vérifier l'expiration de la session
$sessionLifetime = (int)Env::get('SESSION_LIFETIME', 7200);
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $sessionLifetime)) {
    session_unset();
    session_destroy();
    session_start();

    Logger::info('Session expirée', [
        'last_activity' => $_SESSION['last_activity'] ?? 'unknown',
        'timeout' => $sessionLifetime
    ]);
}
$_SESSION['last_activity'] = time();

// 8. Charger le contrôleur de base
require_once __DIR__ . '/controllers/BaseController.php';

// Log du démarrage de l'application
Logger::debug('Application démarrée', [
    'environment' => Env::get('APP_ENV'),
    'debug_mode' => Env::get('APP_DEBUG'),
    'php_version' => PHP_VERSION,
    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
]);
