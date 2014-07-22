<?php

    require_once('config.php');

    @session_start();
    
    $_SESSION = [];

    if(isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 864000, '/instabram_api_php/');
    }

    @session_destroy();

    header('Location: '.SITE_URL);