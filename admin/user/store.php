<?php

use Composer\Pcre\Regex;

$allowedRoute = true;

require_once '../inc/config.php';
require_once '../inc/api_functions.php';

session_start();

$endpoint = 'createUser';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['message'] = ['msg' => ['Metodo não permitido!'], 'color' => 'green', 'type' => 'ERROR'];
    header('Location: create.php/');
}

$json = file_get_contents('php://input');
$type = get_debug_type($json);
$data = json_decode($json,true);
// $data = json_decode($json, true);

$response = api_request($endpoint, 'POST', $data);
$data = [];

if ($response->status == 'ERROR') {
    $data['message'] = ['msg' => $response->message, 'color' => 'red'];
    $data['input_error'] = $response->input_error;
    $data['status'] = 'error';
    echo json_encode($data);
    die;
}

$data['message'] = ['msg' => 'Usuário Criado com sucesso', 'color' => 'green'];
$data['status'] = 'success';
$data['data'] = $response->data;
echo json_encode($data);
die;

