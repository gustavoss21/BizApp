<?php
$allowedRoute = true;

require_once '../inc/config.php';
require_once '../inc/api_functions.php';

session_start();

$endpoint = 'create_client';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['message'] = ['msg' => ['Metodo não permitido!'], 'color' => 'green', 'type' => 'ERROR'];
    header('Location: create.php/');
}

$response = api_request_auth($endpoint, $user, 'POST', $_POST);

if ($response->status == 'ERROR') {
    $_SESSION['message'] = ['msg' => $response->message, 'color' => 'red'];
    $_SESSION['input_error'] = $response->input_error;
    $_SESSION['input_values'] = $_POST;
    header('location: create.php');
    die;
}

$_SESSION['message'] = ['msg' => 'Cliente Criado com sucesso', 'color' => 'green'];
header('location: index.php');
