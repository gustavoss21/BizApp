<?php

require_once '../inc/config.php';
require_once '../inc/api_functions.php';

if(!isset($_GET['id_cliente'])){
    header("Location: index.php/");
}

$id_cliente = $_GET['id_cliente'];
$params = [
    'filter'=>'id_cliente:'.$id_cliente
];

$endpoint = 'get_clients';
$response = api_request($endpoint, 'GET',$params);
// printDebug($response);

$data_client = is_request_error($response);
$data_client = $data_client[0];
$submit_uri = '/projeto_api/app/clientes/edit.php';

$data = [
    'uri' => $submit_uri,
    'inputs' => [
        'id_cliente' => ['identifier' => 'id_cliente', 'label' => '', 'type' => 'hidden', 'value'=>$data_client->id_cliente],
        'email' => ['identifier' => 'email', 'label' => 'Email', 'type' => 'email', 'value'=>$data_client->email],
        'telefone' => ['identifier' => 'telefone', 'label' => 'Telefone', 'type' => 'text', 'value'=>$data_client->telefone],
    ],
    'elements' => [
        'btn-submit' => ['identifier' => 'submit-form', 'class' => 'input-submit input-element', 'tag_type' => 'button', 'label' => 'Atualizar', 'action' => 'type="submit"'],
        'btn-back' => ['identifier' => 'btn-back', 'tag_type' => 'a', 'class' => 'element-back', 'label' => 'Voltar', 'action' => 'href="../clientes"'],
    ]];

$title = 'cliente';
$subtitle = 'Atualizar cliente '.$data_client->nome;
$body = require '../parciais/form.php';
// print_r($body);
require '../app.php';