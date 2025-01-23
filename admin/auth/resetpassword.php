<?php

$allowedRoute = true;

require_once '../parciais/form.php';
require_once '../inc/config.php';
require_once '../inc/api_functions.php';

session_start();

$endpoint = 'get-one-user';
$token_sanitize = filter_input(INPUT_GET, 'token',FILTER_SANITIZE_ENCODED);
$token_decode = urldecode($token_sanitize);
$token_encode = urlencode($token_decode);
$data['filter'] = 'token:'.$token_encode;

$response = api_request($endpoint, 'POST', $data);
$data_user = is_request_error($response);
$data_user = $data_user[0];

$submit_uri = '/projeto_api/admin/auth/commit_resetpassword.php';
$formMethod = 'POST';

$input_error = isset($_SESSION['input_error']) ? $_SESSION['input_error'] : [];
$message = isset($_SESSION['message']) ? $_SESSION['message'] : [];

unset($_SESSION['input_error'], $_SESSION['message']);

// $data = [
//     'uri' => $submit_uri,
//     'inputs' => [
//         'id' => ['identifier' => 'id', 'label' => '', 'type' => 'hidden', 'value' => $data_user->id, 'text_error' => '',  'other_params' => 'required'],
//         'password' => ['identifier' => '', 'label' => 'Senha', 'type' => 'password', 'value' => '', 'text_error' =>'',  'other_params' => 'required'],
//         'password2' => ['identifier' => '', 'label' => 'Corfirme a Senha', 'type' => 'password', 'value' => '', 'text_error' => '',  'other_params' => 'required'],
//     ],
//     'elements' => [
//         'btn-submit' => ['identifier' => 'submit-form', 'class' => 'input-submit input-element', 'tag_type' => 'button', 'label' => 'Atualizar', 'action' => 'type="submit"'],
//         'btn-back' => ['identifier' => 'btn-back', 'tag_type' => 'a', 'class' => 'element-back', 'label' => 'Voltar', 'action' => 'href="../../index.php"'],
//     ]];

$title = 'Usuário';
$subtitle = $data_user->nome.' redefina sua senha';
$idForm = 'updateUseForm';

$form = new SetForm($subtitle,"action='$submit_uri' id='$idForm' method='POST'",$input_error);
$form->setElement('input',['class'=>'input-element','name'=>'token','type'=>'hidden','value'=>$token_encode ?? '']);
$form->setElement('input',['class'=>'input-element','name'=>'password', 'id'=>'password','type'=>'password','senha'],'senha');
$form->setElement('input',['class'=>'input-element','name'=>'password2', 'id'=>'password2','type'=>'password'],'confirme a senha');
$form->setElement('button',['id'=>'submit-form','class'=>'input-submit input-element','type'=>'text','type'=>"submit"],'Atualizar');
$form->setElement('a',['id'=>'btn-back','class'=>'element-back','type'=>'text','href'=>'./status.php?'.$data_user->token],'Voltar para status do usuário');

$body = $form->buildForm();

require '../layout.php';
