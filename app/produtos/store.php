<?php

require_once '../inc/config.php';
require_once '../inc/api_functions.php';

$endpoint = 'create_products';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: create.php/');
}

$data = api_request($endpoint, 'POST', $_POST);

printDebug($data);

// if($er == 'error'){
//     header("Location: create.php/");
// }

// $data = $data->data->data;
// $title = 'clientes';
// $subtitle = 'todos os clientes';
// $body = require '../parciais/list_objets_html.php';

// // print_r($body);
// require '../app.php';
