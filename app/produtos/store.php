<?php

require_once '../inc/config.php';
require_once '../inc/api_functions.php';

session_start();

$endpoint = 'create_products';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['message'] = ['msg' => ['Metodo não permitido!'], 'color' => 'green', 'type' => 'ERROR'];

    header('Location: create.php/');
}

$response = api_request_auth($endpoint, $user, 'POST', $_POST);
// printDebug($response,true);
if ($response->status == 'ERROR') {
    $_SESSION['message'] = ['msg' => $response->message, 'color' => 'red', 'type' => $response->status];
    $_SESSION['input_error'] = $response->input_error;
    $_SESSION['input_values'] = $_POST;
    header('location: create.php');
    die;
}

$_SESSION['message'] = ['msg' => 'produto Criado com sucesso', 'color' => 'green', 'type' => $response->status];
header('location: index.php');
