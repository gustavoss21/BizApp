<?php

$allowedRoute = true;

require_once '../inc/config.php';
require_once '../inc/api_functions.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['message'] = ['msg' => ['Metodo não permitido!'], 'color' => 'red', 'type' => 'ERROR'];
    header('Location: index.php');
}

$endpoint = 'createPreference';

$response = api_request_auth($endpoint, $user, 'GET');
printDebug($response, true);
$endpoint = 'process_payment';

$response = api_request_auth($endpoint, $user, 'POST', $_POST);
exit;
if ($response->status == 'ERROR') {
    $_SESSION['message'] = ['msg' => $response->message, 'color' => 'red', 'type' => $response->status];
    $_SESSION['input_error'] = $response->input_error;
    header('Location: index.php');
    die;
}

$_SESSION['message'] = ['msg' => 'checkout success', 'color' => 'green', 'green' => $response->status];
header('location: success.php');
