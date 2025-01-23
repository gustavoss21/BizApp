<?php
$allowedRoute = true;

require_once '../inc/config.php';
require_once '../inc/api_functions.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['message'] = ['msg' => ['Metodo não permitido!'], 'color' => 'red', 'type' => 'ERROR'];
    header('Location: index.php/');
}

$endpoint = 'reset-password';
$link_base = '/projeto_api/admin/';

//format token
$token_sanitize = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_ENCODED);
$token_decode = urldecode($token_sanitize);
//set request parameters
$data['token'] = $token_decode;
$data['password'] = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_ENCODED);
$data['password2'] = filter_input(INPUT_POST, 'password2', FILTER_SANITIZE_ENCODED);

$response = api_request($endpoint, 'POST', $data);
// printDebug($response,true);

if ($response->status == 'ERROR') {
    $_SESSION['message'] = ['msg' => $response->message, 'color' => 'red', 'type' => $response->status];
    $_SESSION['input_error'] = $response->input_error;
    header("Location: resetpassword.php/?token={$data['token']}");
    die;
}

$_SESSION['message'] = ['msg' => 'Usuário atualizado com sucesso', 'color' => 'green', 'type' => $response->status];
header('location: '.$link_base.'user/status.php?token='.$data['token']);
