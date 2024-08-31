<?php
require_once '../inc/config.php';
require_once '../inc/api_functions.php';

$endpoint = 'get_products';
$params_request = [];
if($_GET['id_produto']){
}
$params_request['filter'] = ['id_produto'=>22];

$data = (api_request($endpoint, 'GET',$params_request));

is_request_error($data);

$data = $data->data;
$title = 'data';
$subtitle = 'veja todos os produtos ativos:';
$body = require '../parciais/list_objets_html.php';

// print_r($body);
require '../app.php';