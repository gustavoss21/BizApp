<?php
$allowedRoute = true;

require_once '../inc/config.php';
require_once '../inc/api_functions.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    $_SESSION['message'] = ['msg' => ['Metodo não permitido!'], 'color' => 'red', 'type' => 'ERROR'];

    header('Location: index.php/');
}
$endpoint = 'active-user';

$response = api_request($endpoint, 'POST', $_GET);
// printDebug($response,true);
$_SESSION['message'] = ['msg' => 'Cliente ativado com sucesso', 'color' => 'green', 'type' => $response->status];
$link_base = '/projeto_api/admin/';

if ($response->status == 'ERROR') {
    $_SESSION['message'] = ['msg' => $response->message, 'color' => 'red', 'type' => $response->status];
}

header('location: '. $link_base);