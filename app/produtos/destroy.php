<?php

require_once '../inc/config.php';
require_once '../inc/api_functions.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    header("Location: index.php/");
}
$endpoint = 'destroy_product';

$request = api_request($endpoint, 'POST', $_POST);
printDebug($request);

// $data = (is_request_error($request))[0];
// // printDebug($data, true);

// $title = 'remover';
// $subtitle = 'remover cliente';
// $link_base = '/projeto_api/app/clientes';
// $body = require '../parciais/confirmation.php';

// // print_r($body);
// require '../app.php';
