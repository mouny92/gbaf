<?php

    /* Déconnexion*/

    /*Suppression des variables de session et de la session*/
    session_start();
    $_SESSION = array();
    session_destroy();
    header('Location: connexion.php');

    /* Suppression des cookies de connexion automatique*/
    setcookie('id', '');
    setcookie('username', '');
    setcookie('firstname', '');
    setcookie('lastname', '');
?>