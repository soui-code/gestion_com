<?php
class ErreurController
{
    function erreur404(){
        include 'views/error/error404.php';
    }

    function erreur500(){
        include 'views/error/error500.php';
    }
}

// Alias pour compatibilité avec le router
class_alias('ErreurController', 'erreur');