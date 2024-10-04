<?php

require_once '../inc/config.php';
require_once '../inc/api_functions.php';

session_start();
// printDebug($_SESSION,true);

if (isset($_SESSION['user']['user_id']) && $_SESSION['user']['user_id']) {
    $_SESSION['message'] = ['msg' => 'Você já esta logado!', 'color' => 'red'];
    unset($_COOKIE['Authorization']);
    header('location: /projeto_api/admin');
    exit;
}

$submit_uri = '/projeto_api/admin/auth/commit_login.php';

$message = isset($_SESSION['message']) ? $_SESSION['message'] : [];
unset($_SESSION['message']);

$input_error = isset($_SESSION['input_error']) ? $_SESSION['input_error'] : [];
unset($_SESSION['input_error']);

$input_values = isset($_SESSION['input_values']) ? $_SESSION['input_values'] : [];
unset($_SESSION['input_values']);

$data = [
    'uri' => $submit_uri,
    'inputs' => [
        'name' => ['identifier' => 'name', 'label' => 'nome', 'type' => 'text', 'value' => $input_values['name'] ?? '', 'text_error' => $input_error->name ?? ''],
        'password' => ['identifier' => 'password', 'label' => 'senha', 'type' => 'password', 'value' => $input_values['password'] ?? '', 'text_error' => $input_error->password ?? ''],
    ],
    'elements' => [
        'btn-submit' => ['identifier' => 'submit-form', 'class' => 'input-submit input-element', 'tag_type' => 'button', 'label' => 'login', 'action' => 'type="submit"'],
        'btn-back' => ['identifier' => 'btn-back', 'tag_type' => 'a', 'class' => 'element-back', 'label' => 'Voltar', 'action' => 'href="../clientes"'],
    ]];
$title = 'login';
$subtitle = 'Faça login';
$body = require '../parciais/form.php';

require '../layout.php';
