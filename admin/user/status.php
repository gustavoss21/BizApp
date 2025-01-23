<?php

$allowedRoute = true;

require_once '../inc/config.php';
require_once '../inc/api_functions.php';
require_once '../parciais/form.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {//add security
    header('Location: status.php/');

    $_SESSION['message'] = ['msg' => ['Metodo não permitido!'], 'color' => 'red', 'type' => 'ERROR'];

    header('Location: index.php/');
}

$formMethod = 'GET';
$message = isset($_SESSION['message']) ? $_SESSION['message'] : [];
$input_error = isset($_SESSION['input_error']) ? $_SESSION['input_error'] : [];
$input_values = isset($_SESSION['input_values']) ? $_SESSION['input_values'] : [];

unset($_SESSION['input_error'], $_SESSION['message'], $_SESSION['input_values']);

$title = 'Status do usuário';
$subtitle = 'pesquisar usuário';
$idForm = 'searchForm';
$submit_uri = 'status.php';



$form = new SetForm($subtitle,"action='$submit_uri' id='$idForm'",$input_error);

$form->setElement('input',['class'=>'input-element','name'=>'nome', 'id'=>'nome','type'=>'text','value'=>$input_values['name'] ?? ''],'nome do usuário');
// $form->setElement('input',['class'=>'input-element','name'=>'token', 'id'=>'token','type'=>'text','value'=>$input_values['token'] ?? ''],'Usuario | token');
$form->setElement('input',['class'=>'input-element','name'=>'email', 'id'=>'email','type'=>'email','value'=>$input_values['email'] ?? ''],'email do usuário');
$form->setElement('button',['id'=>'submit-form','class'=>'input-submit input-element','type'=>'text','type'=>"submit",'value'=>$input_values['name'] ?? ''],'Pesquisar');
$form->setElement('p',['id'=>'rule-form'],'preencha pelo menos 1 dos campos');

$body = $form->buildForm();

$filter_array = [];

if (empty($_GET)) {
    require '../layout.php';
}

foreach ($_GET as $filter => $value) {
    if (!$value) {
        continue;
    }

    $token_sanitize = filter_input(INPUT_GET, $filter, FILTER_SANITIZE_ENCODED);
    $token_decode = urldecode($token_sanitize);
    $velue_security = urlencode($token_decode);



    $filter_array[] = $filter . ':' . $velue_security;
}

$filters['filter'] = implode(';', $filter_array);
// $response = api_request($endpoint,'POST', $filters);

if (empty($filters['filter'])) {
    require_once '../layout.php';
    exit;
}

$endpoint = 'get-one-user';

$response = api_request($endpoint, 'POST', $filters);

if (!$response) {
    $message['msg'] = 'houve um erro inesperado';
    $message['color'] = 'red';
    return require '../layout.php';
}

if ($response->status == 'ERROR') {
    $_SESSION['message'] = ['msg' => $response->message, 'color' => 'red', 'type' => $response->status];
    return require '../layout.php';
}

if(!$response->data){
    $_SESSION['message'] = ['msg' => 'usuário não encontrado', 'color' => 'red', 'type' => 'ERROR'];
    return require '../layout.php';
}

[$userData,$paymentData] = $response->data;

$templateUser = require_once '../parciais/user_show.php';
$body = '<div id="content-search-user"><div style="flex:5">' . $body . '</div><div id="user-data">' . $templateUser . '</div></div>';

require '../layout.php';

// printDebug($response);
