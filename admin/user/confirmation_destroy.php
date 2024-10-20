<?php

require_once '../inc/config.php';
require_once '../inc/api_functions.php';

session_start();

$message = '';
$endpoint = 'superAuthorizationRequired';
$response = api_request($endpoint, 'GET');

if ($response->status == 'ERROR') {
    $_SESSION['message'] = ['msg' => $response->message, 'color' => 'red'];
    $_SESSION['input_error'] = $response->input_error;
    $_SESSION['input_values'] = $_POST;
    header('location: ../../');
    exit;
}
// printDebug($response, true);

$endpoint = 'getUsers';

if (!isset($_GET['id'])) {
    header('Location: index.php/');
}

$parameters = [
    'filter' => "id:{$_GET['id']}"
];

$request = api_request($endpoint, 'GET', $parameters);
$data = (is_request_error($request));
$data = $data[0];
$message = isset($_SESSION['message']) ? $_SESSION['message'] : [];
unset($_SESSION['message']);

$title = 'remover';
$subtitle = 'remover usuário';
$link_base = '/projeto_api/admin';
$submit_link = $link_base . '/user/destroy.php';
$parameter_id = 'id';
$item_name = 'nome';

$body = require '../parciais/confirmation.php';

require '../layout.php';
