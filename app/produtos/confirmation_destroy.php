<?php

require_once '../inc/config.php';
require_once '../inc/api_functions.php';

$endpoint = 'get_products';
$parameters = [
    'filter'=>"id_produto:{$_GET['id_produto']}"
];

$request = api_request($endpoint, 'GET', $parameters);
// printDebug($request,true);
$data = (is_request_error($request));
$data = $data[0];

$title = 'remover';
$subtitle = 'remover clientes';
$link_base = '/projeto_api/app/clientes';
$submit_link = '/projeto_api/app/produtos/destroy.php';
$parameter_id = 'id_produto';
$item_name = 'produto';

$body = require '../parciais/confirmation.php';

require '../app.php';
