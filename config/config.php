<?php
define('BASE_URL', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
    . '://' . $_SERVER['HTTP_HOST'] . '/gestion_commerciale');

function redirect($url)
{
    header('Location: ' . BASE_URL . '/' . ltrim($url, '/'));
    exit;
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
        if ($type === 'success') {
            $icon = 'fa-check-circle';
        }
        elseif ($type === 'danger') {
            $icon = 'fa-exclamation-triangle';
        }
        elseif ($type === 'warning') {
            $icon = 'fa-exclamation-circle';
        }
        else {
            $icon = 'fa-info-circle';
        }

        echo '<div class="alert alert-' . $type . ' alert-dismissible fade show shadow-sm text-center" role="alert" style="border-radius: 8px; margin-bottom: 20px; font-weight: 500;">';
        echo '<i class="fa ' . $icon . ' mr-2"></i>';
        echo htmlspecialchars($message);
        echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
        echo '<span aria-hidden="true">&times;</span>';
        echo '</button>';
        echo '</div>';

        unset($_SESSION['flash']);
    }
}
