<?php

require_once '../inc/config.php';
require_once '../inc/api_functions.php';

$endpoint = 'get_clients';

$request = api_request($endpoint, 'GET', $_GET);
// printDebug($request, true);

$data = is_request_error($request);

$title = 'clientes';
$subtitle = 'clientes';
$link_base = '/projeto_api/app/clientes';
$link_create = '/projeto_api/app/clientes/create.php';
$link_delete = '/projeto_api/app/clientes/confirmation_destroy.php/?id_cliente=';
$body = require '../parciais/list_objets_html.php';

// print_r($body);
require '../app.php';
