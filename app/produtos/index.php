<?php

require_once '../inc/config.php';
require_once '../inc/api_functions.php';

$endpoint = 'get_products';
$params_request = [];

$data = (api_request($endpoint, 'GET'));
is_request_error($data);

$data = $data->data->data;
$title = 'produtos';
$subtitle = 'todos os produtos';
$body = require '../parciais/list_objets_html.php';

// print_r($body);
require '../app.php';
