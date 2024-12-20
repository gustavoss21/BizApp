<?php

$allowedRoute = true;

require_once '../inc/config.php';
require_once '../inc/api_functions.php';
require_once '../parciais/form.php';


session_start();
// printDebug($_SESSION,true);

if (isset($_SESSION['user']['user_id']) && $_SESSION['user']['user_id']) {
    $_SESSION['message'] = ['msg' => 'Você já esta logado!', 'color' => 'red'];
    unset($_COOKIE['Authorization']);
    header('location: /projeto_api/admin');
    exit;
}

$submit_uri = '/projeto_api/admin/auth/commit_login.php';
$formMethod = 'POST';
$message = isset($_SESSION['message']) ? $_SESSION['message'] : [];
$input_error = isset($_SESSION['input_error']) ? $_SESSION['input_error'] : [];
$input_values = isset($_SESSION['input_values']) ? $_SESSION['input_values'] : [];

unset($_SESSION['message'], $_SESSION['input_error'], $_SESSION['input_values']);

$title = 'login';
$subtitle = 'Faça login';
$idForm = 'loginForm';

$form = new SetForm($subtitle,"action='$submit_uri' id='$idForm'",$input_error);
$form->setElement('input',['class'=>'input-element','name'=>'name', 'id'=>'name','type'=>'text','value'=>$input_values['name'] ?? ''],'nome do usuário');
$form->setElement('input',['class'=>'input-element','name'=>'password', 'id'=>'password','type'=>'password','value'=>$input_values['password'] ?? ''],'senha do usuário');
$form->setElement('button',['id'=>'submit-form','class'=>'input-submit input-element','type'=>'text','type'=>"submit"],'Login');
$form->setElement('a',['id'=>'btn-back','class'=>'element-back','type'=>'text','href'=>'../clientes'],'Voltar');

$body = $form->buildForm();

require '../layout.php';
