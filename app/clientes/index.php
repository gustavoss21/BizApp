<?php

require_once '../inc/config.php';
require_once '../inc/api_functions.php';

session_start();

$message = [];
$endpoint = 'get_clients';

$paramenters = ['filter' => implode(';', ['active:true', ...$_GET])];
$request = api_request($endpoint, 'GET', $paramenters);

$data = is_request_error($request);

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
}

unset($_SESSION['message']);

$title = 'clientes';
$subtitle = 'clientes';
$link_base = '/projeto_api/app/clientes';
$link_create = '/projeto_api/app/clientes/create.php';
$link_delete = '/projeto_api/app/clientes/confirmation_destroy.php/?id_cliente=';
$link_update = '/projeto_api/app/clientes/update.php/?id_cliente=';
$body = require '../parciais/list_objets_html.php';

require '../app.php';
