<?php

require_once '../inc/config.php';
require_once '../inc/api_functions.php';

$endpoint = 'get_clients';
$parameters = [
    'filter'=>"id_cliente:{$_GET['id_cliente']}"
];

$request = api_request($endpoint, 'GET', $parameters);
// printDebug($request,true);
$data = (is_request_error($request));
$data = $data[0];
// if($request['status'] == 'ERROR'){

// }

$title = 'remover';
$subtitle = 'remover clientes';
$link_base = '/projeto_api/app/clientes';
$submit_link = '/projeto_api/app/clientes/destroy.php';
$parameter_id = 'id_cliente';
$item_name = 'nome';
$body = require '../parciais/confirmation.php';

// print_r($body);
require '../app.php';
