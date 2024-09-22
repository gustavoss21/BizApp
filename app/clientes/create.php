<?php

require_once '../inc/config.php';
require_once '../inc/api_functions.php';

session_start();

$submit_uri = '/projeto_api/app/clientes/store.php';

$message = isset($_SESSION['message']) ? $_SESSION['message'] : [];
unset($_SESSION['message']);

$input_error = isset($_SESSION['input_error']) ? $_SESSION['input_error'] : [];
unset($_SESSION['input_error']);

$input_values = isset($_SESSION['input_values']) ? $_SESSION['input_values'] : [];
unset($_SESSION['input_values']);

$data = [
    'uri' => $submit_uri,
    'inputs' => [
        'nome' => ['identifier' => 'nome', 'label' => 'nome', 'type' => 'text', 'value' => $input_values['nome'] ?? '', 'text_error' => $input_error->nome ?? ''],
        'email' => ['identifier' => 'email', 'label' => 'email', 'type' => 'email', 'value' => $input_values['email'] ?? '', 'text_error' => $input_error->email ?? ''],
        'telefone' => ['identifier' => 'telefone', 'label' => 'telefone', 'type' => 'text', 'value' => $input_values['telefone'] ?? '', 'text_error' => $input_error->telefone ?? ''],
    ],
    'elements' => [
        'btn-submit' => ['identifier' => 'submit-form', 'class' => 'input-submit input-element', 'tag_type' => 'button', 'label' => 'Criar', 'action' => 'type="submit"'],
        'btn-back' => ['identifier' => 'btn-back', 'tag_type' => 'a', 'class' => 'element-back', 'label' => 'Voltar', 'action' => 'href="../clientes"'],
    ]];
$title = 'cliente';
$subtitle = 'novo cliente';
$body = require '../parciais/form.php';

require '../app.php';
