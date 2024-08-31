<?php
require_once '../inc/config.php';
require_once '../inc/api_functions.php';

$endpoint = 'get_clients';
$data = (api_request($endpoint, 'GET'));
print_r($data);
return;
is_request_error($data);

$data = $data->data;
$title = 'data';
$subtitle = 'veja todos os clientes ativos:';
$body = require '../parciais/list_objets_html.php';

// print_r($body);
require '../app.php';