<?php

require_once '../inc/config.php';
require_once '../inc/api_functions.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['message'] = ['msg' => ['Metodo não permitido!'], 'color' => 'red', 'type' => 'ERROR'];
    header('Location: index.php/');
}

$endpoint = 'updateUser';
$link_base = '/projeto_api/admin/';

$response = api_request($endpoint, 'POST', $_POST);
// printDebug($response,true);

if ($response->status == 'ERROR') {
    $_SESSION['message'] = ['msg' => $response->message, 'color' => 'red', 'type' => $response->status];
    $_SESSION['input_error'] = $response->input_error;
    header("Location: update.php/?id={$_POST['id']}");
    die;
}

$_SESSION['message'] = ['msg' => 'Usuário atualizado com sucesso', 'color' => 'green', 'type' => $response->status];
header('location: '.$link_base);
