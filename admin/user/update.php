<?php

require_once '../inc/config.php';
require_once '../inc/api_functions.php';

session_start();

if (!isset($_GET['id'])) {
    $_SESSION['message'] = ['msg' => ['houve um error inesperado, verifique os parâmetros de requisição!'], 'color' => 'green', 'type' => 'ERROR'];
    header('Location: index.php/');
}

$endpoint = 'getUsers';
$paramers = ['filter' => 'id:'.$_GET['id']];
// printDebug($paramers, true);
$response = api_request($endpoint, 'GET', $paramers);

$data_user = is_request_error($response);
$data_user = $data_user[0];
$submit_uri = '/projeto_api/admin/user/edit.php';

$input_error = isset($_SESSION['input_error']) ? $_SESSION['input_error'] : [];
unset($_SESSION['input_error']);

$message = isset($_SESSION['message']) ? $_SESSION['message'] : [];
unset($_SESSION['message']);
$data = [
    'uri' => $submit_uri,
    'inputs' => [
        'id' => ['identifier' => 'id', 'label' => '', 'type' => 'hidden', 'value' => $data_user->id, 'text_error' => '',  'other_params' => 'required'],
        'nome' => ['identifier' => 'nome', 'label' => 'Nome', 'type' => 'text', 'value' => $data_user->nome, 'text_error' => $input_error->nome ?? '',  'other_params' => 'required'],
        'tokken' => ['identifier' => '', 'label' => 'tokken', 'type' => 'text', 'value' => $data_user->tokken, 'text_error' => $input_error->tokken ?? '',  'other_params' => 'required'],
        'password' => ['identifier' => '', 'label' => 'Senha', 'type' => 'password', 'value' => str_repeat('x', 32), 'text_error' => $input_error->password ?? '',  'other_params' => 'required'],
    ],
    'elements' => [
        'btn-new-tokken' => ['identifier' => 'new-tokken-form', 'class' => 'new-tokken-form input-element', 'tag_type' => 'a', 'label' => 'Gerar nova senha e tokken', 'action' => 'href="?new_tokken=true&id='.$_GET['id'].'"'],
        'btn-submit' => ['identifier' => 'submit-form', 'class' => 'input-submit input-element', 'tag_type' => 'button', 'label' => 'Atualizar', 'action' => 'type="submit"'],
        'btn-back' => ['identifier' => 'btn-back', 'tag_type' => 'a', 'class' => 'element-back', 'label' => 'Voltar', 'action' => 'href="../../index.php"'],
    ]];

if(isset($_GET['new_tokken']) and !empty($_GET['new_tokken'])){
    $char_token = 'AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz123456789';
    $GerarHashAleatorio = fn ($caracteres, $tamanho) => substr(str_shuffle($caracteres), 0, $tamanho);
    $data['inputs']['tokken']['value'] = $GerarHashAleatorio($char_token, 32);
    $data['inputs']['tokken']['identifier'] = 'tokken';
    $data['inputs']['password']['identifier'] = 'password';
    $data['inputs']['password']['value'] = $GerarHashAleatorio($char_token, 32);
    $data['inputs']['password']['type'] = 'text';
    // printDebug($data);

}


$title = 'Usuário';
$subtitle = 'Atualizar Usuário ' . $data_user->nome;
$body = require '../parciais/form.php';
require '../layout.php';
