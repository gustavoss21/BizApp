<?php
$allowedRoute = true;

require_once '../inc/config.php';
require_once '../inc/api_functions.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['message'] = ['msg' => ['Metodo não permitido!'], 'color' => 'red', 'type' => 'ERROR'];
    header('Location: index.php/');
}

$endpoint = 'update_product';

$response = api_request_auth($endpoint, $user, 'POST', $_POST);

if ($response->status == 'ERROR') {
    $_SESSION['message'] = ['msg' => $response->message, 'color' => 'red', 'type' => $response->status];
    $_SESSION['input_error'] = $response->input_error;
    header("Location: update.php/?id={$_POST['id']}");
    die;
}

$_SESSION['message'] = ['msg' => 'produto atualizado com sucesso', 'color' => 'green', 'green' => $response->status];
header('location: index.php');
