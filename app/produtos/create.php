<?php

require_once '../inc/config.php';
require_once '../inc/api_functions.php';

session_start();

$url_base = '/projeto_api/app/produtos';
$submit_uri = $url_base . '/store.php';

$message = isset($_SESSION['message']) ? $_SESSION['message'] : [];
unset($_SESSION['message']);

$input_error = isset($_SESSION['input_error']) ? $_SESSION['input_error'] : [];
unset($_SESSION['input_error']);

$input_values = isset($_SESSION['input_values']) ? $_SESSION['input_values'] : [];
unset($_SESSION['input_values']);
$data = [
    'uri' => $submit_uri,
    'inputs' => [
        'produto' => ['identifier' => 'produto', 'label' => 'Nome do produto', 'type' => 'text',  'value' => $input_values['produto'] ?? '', 'text_error' => $input_error->produto ?? ''],
        'quantidade' => ['identifier' => 'quantidade', 'label' => 'Quantidade em estoque', 'type' => 'number', 'value' => $input_values['estoque'] ?? '', 'text_error' => $input_error->quantidade ?? ''],
    ],
    'elements' => [
        'btn-subtmit' => ['identifier' => 'submit-form', 'class' => 'input-submit input-element', 'tag_type' => 'button', 'label' => 'Criar', 'action' => 'type="submit"'],
        'btn-back' => ['identifier' => 'btn-back', 'tag_type' => 'a', 'class' => 'element-back', 'label' => 'Voltar', 'action' => 'href="'.$url_base.'"'],
    ]];
$title = 'Prodotuo';
$subtitle = 'novo prodotuo';
$body = require '../parciais/form.php';

require '../app.php';
