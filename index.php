<?php
require 'config/config.php';
if (empty($_SESSION['utilisateur_id'])) {
    header('location:' . BASE_URL . '/utilisateur/dashboard');
} else {
    header('location:' . BASE_URL . '/utilisateur/connexion');
}

?>