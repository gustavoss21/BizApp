<?php
$allowedRoute = true;

require_once '../inc/config.php';
require_once '../inc/api_functions.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['message'] = ['msg' => ['Metodo não permitido!'], 'color' => 'red', 'type' => 'ERROR'];

    header('Location: index.php/');
}
$endpoint = 'destroy-client';

$response = api_request_auth($endpoint, $user, 'POST', $_POST);

$_SESSION['message'] = ['msg' => 'Cliente removido com sucesso', 'color' => 'green', 'type' => $response->status];

if ($response->status == 'ERROR') {
    $_SESSION['message'] = ['msg' => $response->message, 'color' => 'red', 'type' => $response->status];
}

header('location: index.php');
