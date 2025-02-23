<?php
$allowedRoute = true;

require_once '../inc/config.php';
require_once '../inc/api_functions.php';

session_start();

$message = '';
$endpoint = 'has-super-authorization';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['message'] = ['msg' => ['Metodo não permitido!'], 'color' => 'red', 'type' => 'ERROR'];
    header('Location: login.php/');
    exit;
}

$response = api_request_auth($endpoint,$_POST,'GET',$_POST);

if ($response->status == 'ERROR') {
    $_SESSION['message'] = ['msg' => 'credenciais inválidas', 'color' => 'red'];
    $_SESSION['input_error'] = $response->input_error;
    $_SESSION['input_values'] = $_POST;
    header('location: login.php');
    exit;
}

$credenciais = base64_encode("{$_POST['name']}:{$_POST['password']}");
$_SESSION['message'] = ['msg' => 'Você esta logado!', 'color' => 'green'];
$_SESSION['user'] = ['user_id' => $response->data[0]->id, 'username' => $response->data[0]->nome];
$_SESSION['Authorization'] = $credenciais;
setcookie("Authorization", $credenciais); 

// echo $_COOKIE["Authorization"];
// echo setcookie("teste",'123456789',0,'','',true);
// printDebug($_SESSION,true);
header('location: /projeto_api/admin');
