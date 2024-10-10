<?php

require_once '../inc/config.php';
require_once '../inc/api_functions.php';

session_start();

$endpoint = 'create_user';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['message'] = ['msg' => ['Metodo não permitido!'], 'color' => 'green', 'type' => 'ERROR'];
    header('Location: create.php/');
}

$response = api_request($endpoint, 'POST', $_POST);
// printDebug($response,true);
if ($response->status == 'ERROR') {
    $_SESSION['message'] = ['msg' => $response->message, 'color' => 'red'];
    $_SESSION['input_error'] = $response->input_error;
    $_SESSION['input_values'] = $_POST;
    header('location: create.php');
    die;
}

$_SESSION['message'] = ['msg' => 'Usuário Criado com sucesso', 'color' => 'green'];
header('location: ../index.php');
