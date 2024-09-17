<?php

require_once '../inc/config.php';
require_once '../inc/api_functions.php';

if(!isset($_GET['id_produto'])){
    header("Location: index.php/");
}

$id_cliente = $_GET['id_produto'];
$params = [
    'filter'=>'id_produto:'.$id_cliente
];

$endpoint = 'get_products';
$response = api_request($endpoint, 'GET',$params);

$data_product = is_request_error($response);
$data_product = $data_product[0];
$submit_uri = '/projeto_api/app/produtos/edit.php';

$data = [
    'uri' => $submit_uri,
    'inputs' => [
        'id_product' => ['identifier' => 'id_produto', 'label' => '', 'type' => 'hidden', 'value'=>$data_product->id_produto],
        'produto' => ['identifier' => 'produto', 'label' => 'produto', 'type' => 'text', 'value'=>$data_product->produto],
        'quantidade' => ['identifier' => 'quantidade', 'label' => 'quantidade', 'type' => 'number', 'value'=>$data_product->quantidade],
    ],
    'elements' => [
        'btn-submit' => ['identifier' => 'submit-form', 'class' => 'input-submit input-element', 'tag_type' => 'button', 'label' => 'Atualizar', 'action' => 'type="submit"'],
        'btn-back' => ['identifier' => 'btn-back', 'tag_type' => 'a', 'class' => 'element-back', 'label' => 'Voltar', 'action' => 'href="../index.php"'],
    ]];

$title = 'Produto';
$subtitle = 'Atualizar produto '.$data_product->produto;
$body = require '../parciais/form.php';
require '../app.php';