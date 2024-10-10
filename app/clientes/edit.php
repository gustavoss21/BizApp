<?php

require_once '../inc/config.php';
require_once '../inc/api_functions.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['message'] = ['msg' => ['Metodo não permitido!'], 'color' => 'red', 'type' => 'ERROR'];
    header('Location: index.php/');
}

$endpoint = 'update_client';

$response = api_request_auth($endpoint, $user, 'POST', $_POST);

if ($response->status == 'ERROR') {
    $_SESSION['message'] = ['msg' => $response->message, 'color' => 'red', 'type' => $response->status];
    $_SESSION['input_error'] = $response->input_error;
    header("Location: update.php/?id_cliente={$_POST['id_cliente']}");
    die;
}

$_SESSION['message'] = ['msg' => 'Cliente atualizado com sucesso', 'color' => 'green', 'type' => $response->status];
header('location: index.php');
