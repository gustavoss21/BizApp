<?php

require_once '../inc/config.php';
require_once '../inc/api_functions.php';

$submit_uri = '/projeto_api/app/clientes/store.php';

$data = [
    'uri' => $submit_uri,
    'inputs' => [
        'nome' => ['identifier' => 'nome', 'label' => 'nome', 'type' => 'text', 'value'=>''],
        'email' => ['identifier' => 'email', 'label' => 'email', 'type' => 'email', 'value'=>''],
        'telefone' => ['identifier' => 'telefone', 'label' => 'telefone', 'type' => 'text', 'value'=>''],
    ],
    'elements' => [
        'btn-submit' => ['identifier' => 'submit-form', 'class' => 'input-submit input-element', 'tag_type' => 'button', 'label' => 'Criar', 'action' => 'type="submit"'],
        'btn-back' => ['identifier' => 'btn-back', 'tag_type' => 'a', 'class' => 'element-back', 'label' => 'Voltar', 'action' => 'href="../clientes"'],
    ]];
$title = 'cliente';
$subtitle = 'novo cliente';
$body = require '../parciais/form.php';

require '../app.php';
