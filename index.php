<?php
// Bootstrap charge automatiquement la session
require_once __DIR__ . '/bootstrap.php';

if (empty($_SESSION['utilisateur_id'])) {
    header('location:' . BASE_URL . '/utilisateur/connexion');
} else {
    header('location:' . BASE_URL . '/utilisateur/dashboard');
}
exit;