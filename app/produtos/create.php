<?php

require_once '../inc/config.php';
require_once '../inc/api_functions.php';
$url_base = '/projeto_api/app/produtos';
$submit_uri = $url_base . '/store.php';
$data = [
    'uri' => $submit_uri,
    'inputs' => [
        'produto' => ['identifier' => 'produto', 'label' => 'Nome do produto', 'type' => 'text', 'value'=>''],
        'quantidade' => ['identifier' => 'quantidade', 'label' => 'Quantidade em estoque', 'type' => 'number', 'value'=>''],
    ],
    'elements' => [
        'btn-subtmit' => ['identifier' => 'submit-form', 'class' => 'input-submit input-element', 'tag_type' => 'button', 'label' => 'Criar', 'action' => 'type="submit"'],
        'btn-back' => ['identifier' => 'btn-back', 'tag_type' => 'a', 'class' => 'element-back', 'label' => 'Voltar', 'action' => 'href="'.$url_base.'"'],
    ]];
$title = 'cliente';
$subtitle = 'novo cliente';
$body = require '../parciais/form.php';

// print_r($body);
require '../app.php';
