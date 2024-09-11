<?php

require_once '../inc/config.php';
require_once '../inc/api_functions.php';

$endpoint = 'get_products';

$data = (api_request($endpoint, 'GET',$_GET));
// printDebug($data,true);

$data = is_request_error($data);
$title = 'produtos';
$subtitle = 'todos os produtos';
$link_base = '/projeto_api/app/produtos';
$link_delete = $link_base.'/confirmation_destroy.php/?id_produto=';
$link_create = $link_base . '/create.php';
$body = require '../parciais/list_objets_html.php';

// print_r($body);
require '../app.php';
