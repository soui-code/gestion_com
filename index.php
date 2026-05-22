<?php
require 'config/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['utilisateur_id'])) {
    header('location:' . BASE_URL . '/utilisateur/connexion');
} else {
    header('location:' . BASE_URL . '/utilisateur/dashboard');
}

?>