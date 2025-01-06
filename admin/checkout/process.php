<?php

$allowedRoute = true;

require_once '../inc/config.php';
require_once '../inc/api_functions.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    $_SESSION['message'] = ['msg' => ['Metodo não permitido!'], 'color' => 'red', 'type' => 'ERROR'];

    header('Location: index.php');
}

$endpoint = 'create-preference';

// $response = api_request_auth($endpoint, $user, 'GET');
$endpoint = 'process-payment';
$json = file_get_contents('php://input');
$data = json_decode($json, true);
$TES = json_decode($json);

$response = api_request($endpoint,'POST', $data);

echo json_encode($response);

