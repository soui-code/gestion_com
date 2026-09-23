<?php

// Charger les dépendances
require_once __DIR__ . '/env.php';
require_once __DIR__ . '/logger.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/helpers.php';

// Charger les variables d'environnement
try {
    Env::load(dirname(__DIR__) . '/.env');
} catch (Exception $e) {
    die('Erreur de configuration : Fichier .env introuvable. Copiez .env.example vers .env');
}

// Configuration de base
define('BASE_URL', Env::get('APP_URL'));

// Gestionnaire d'erreurs personnalisé
set_error_handler(function($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return;
    }

    $errorMessage = "Erreur [$severity]: $message dans $file à la ligne $line";
    Logger::error($errorMessage);

    if (isDebug()) {
        throw new ErrorException($message, 0, $severity, $file, $line);
    }
});

// Gestionnaire d'exceptions
set_exception_handler(function($exception) {
    Logger::logException($exception);

    if (isDebug()) {
        echo '<div style="background: #f8d7da; color: #721c24; padding: 20px; border: 1px solid #f5c6cb; border-radius: 5px; margin: 20px;">';
        echo '<h3>Exception: ' . e(get_class($exception)) . '</h3>';
        echo '<p><strong>Message:</strong> ' . e($exception->getMessage()) . '</p>';
        echo '<p><strong>Fichier:</strong> ' . e($exception->getFile()) . ':' . e($exception->getLine()) . '</p>';
        echo '<pre>' . e($exception->getTraceAsString()) . '</pre>';
        echo '</div>';
    } else {
        http_response_code(500);
        echo '<div style="text-align: center; padding: 50px;">';
        echo '<h2>Une erreur est survenue</h2>';
        echo '<p>Veuillez réessayer plus tard ou contacter l\'administrateur.</p>';
        echo '</div>';
    }
});

function redirect($url)
{
    header('Location: ' . BASE_URL . '/' . ltrim($url, '/'));
    exit;
}

/**
 * Récupère l'ID de la boutique active selon le rôle de l'utilisateur
 * - Caissier : boutique_active (initialisée depuis id_boutique)
 * - Manager : boutique_active (sélectionnée manuellement)
 * - Admin : null
 *
 * @return int|null ID de la boutique active ou null
 */
function getBoutiqueActive()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    return $_SESSION['boutique_active'] ?? null;
}

/**
 * Récupère l'ID de la société selon le rôle de l'utilisateur
 * - Admin consultant une société : admin_view_societe
 * - Manager/Caissier : id_societe
 * - Admin normal : null
 *
 * @return int|null ID de la société ou null
 */
function getSocieteActive()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Si admin consulte une société spécifique
    if (($_SESSION['role'] ?? '') === 'admin' && !empty($_SESSION['admin_view_societe'])) {
        return $_SESSION['admin_view_societe'];
    }

    // Sinon, retourner la société de l'utilisateur
    return $_SESSION['id_societe'] ?? null;
}

// === FLASH MESSAGES SYSTEM ===
function setFlash($type, $message)
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['flash'] = [
        'type' => $type, // 'success', 'danger', 'warning', 'info'
        'message' => $message
    ];

    Logger::info("Flash message set: [$type] $message");
}

function displayFlash()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (isset($_SESSION['flash'])) {
        $type = $_SESSION['flash']['type'] === 'error' ? 'danger' : $_SESSION['flash']['type'];
        $message = $_SESSION['flash']['message'];
        $icon = '';
        $bgColor = '';

        if ($type === 'success') {
            $icon = 'fa-check-circle';
            $bgColor = '#28a745';
        }
        elseif ($type === 'danger') {
            $icon = 'fa-times-circle';
            $bgColor = '#dc3545';
        }
        elseif ($type === 'warning') {
            $icon = 'fa-exclamation-triangle';
            $bgColor = '#ffc107';
        }
        else {
            $icon = 'fa-info-circle';
            $bgColor = '#17a2b8';
        }

        echo '<div id="flashToast" style="position:fixed;top:20px;right:20px;z-index:9999;min-width:300px;max-width:400px;background:' . $bgColor . ';color:#fff;padding:20px;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.3);display:none;animation:slideIn 0.3s ease-out;">
            <div style="display:flex;align-items:center;gap:15px;">
                <i class="fa ' . e($icon) . '" style="font-size:32px;"></i>
                <div style="flex:1;">
                    <strong style="display:block;margin-bottom:5px;font-size:16px;">' . ($type === 'success' ? 'Succès' : ($type === 'danger' ? 'Erreur' : ucfirst($type))) . '</strong>
                    <div style="font-size:14px;">' . e($message) . '</div>
                </div>
                <button onclick="document.getElementById(\'flashToast\').remove();" style="background:none;border:none;color:#fff;font-size:20px;cursor:pointer;padding:0;line-height:1;">&times;</button>
            </div>
        </div>
        <style>
            @keyframes slideIn {
                from { transform: translateX(400px); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOut {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(400px); opacity: 0; }
            }
        </style>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var toast = document.getElementById("flashToast");
                if (toast) {
                    toast.style.display = "block";
                    setTimeout(function() {
                        toast.style.animation = "slideOut 0.3s ease-in";
                        setTimeout(function() { toast.remove(); }, 300);
                    }, 3000);
                }
            });
        </script>';

        unset($_SESSION['flash']);
    }
}
