<?php

declare(strict_types=1);

use Firebase\JWT\JWT;

require_once("config.php");
require_once('vendor/autoload.php');


$link = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if($link === false){
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
if(empty(trim($_POST["username"]))){
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    die("Please enter username.");
} else{
    $username = trim($_POST["username"]);
}

if(empty(trim($_POST["password"]))){
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    die("Please enter your password.");
} else{
    $password = trim($_POST["password"]);
}

$sql = "SELECT id, username, password FROM users WHERE username = ?";
$stmt = mysqli_prepare($link, $sql);
if (!$stmt) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    die('failed creating statement');
}
mysqli_stmt_bind_param($stmt, "s", $param_username);
$param_username = $username;

if(mysqli_stmt_execute($stmt)){
    mysqli_stmt_store_result($stmt);
    if(mysqli_stmt_num_rows($stmt) == 1){
        mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
        if(mysqli_stmt_fetch($stmt)){
            if(password_verify($password, $hashed_password)){
                $hasValidCredentials = true;
            } else{
                header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
                die("Invalid username or password.");
            }
        }
    } else{
        header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
        die("Invalid username or password.");
    }
} else{
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    die("Oops! Something went wrong. Please try again later.");
}

mysqli_stmt_close($stmt);
mysqli_close($link);

if ($hasValidCredentials) {
    $secretKey  = SECRET;
    $tokenId    = base64_encode(random_bytes(16));
    $issuedAt   = new DateTimeImmutable();
    $expire     = $issuedAt->modify('+6 minutes')->getTimestamp();

    $data = [
        'iat'  => $issuedAt->getTimestamp(),   
        'jti'  => $tokenId,                
        'nbf'  => $issuedAt->getTimestamp(),   
        'exp'  => $expire,                     
        'userName' => $username,
    ];

    $jwtValue = JWT::encode(
        $data,      
        $secretKey, 
        'HS512'     
    );

    setcookie("auth", $jwtValue);

    echo $jwtValue;
}
