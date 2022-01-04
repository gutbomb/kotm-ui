<?php
    $secret_key = "KOTMJWTAPP";
    $jwt = null;
    $authHeader = explode(" ", $_SERVER['HTTP_AUTHORIZATION']);
    $jwt = $authHeader[1];
?>