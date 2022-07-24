<?php

declare(strict_types=1);

use Firebase\JWT\JWT;

require_once("config.php");
require_once('vendor/autoload.php');

if (!isset($_SERVER['HTTP_AUTHORIZATION']) || ! preg_match('/Bearer\s(\S+)/', $_SERVER['HTTP_AUTHORIZATION'], $matches)) {
    if (!isset($_COOKIE['auth'])) {
        header('HTTP/1.0 400 Bad Request');
        echo 'Token not found in request';
        exit;
    }
    $matches = array("", $_COOKIE['auth']);
}

$jwt = $matches[1];
if (! $jwt) {
    // No token was able to be extracted from the authorization header
    header('HTTP/1.0 400 Bad Request');
    exit;
}

$token = JWT::decode($jwt, SECRET, ['HS512']);
$now = new DateTimeImmutable();

if ($token->userName !== 'admin')
{
    header('HTTP/1.1 401 Unauthorized');
    exit;
}

