<?php

require_once '../inc/config.php';
require_once '../inc/api_functions.php';

$endpoint = 'get_products';
$paramenters = [
    'filter'=>'active:true',
    ...$_GET
];

$data = (api_request($endpoint, 'GET',$paramenters));

$data = is_request_error($data);
$title = 'produto';
$subtitle = 'todos os produtos';
$link_base = '/projeto_api/app/produtos';
$link_delete = $link_base.'/confirmation_destroy.php/?id_produto=';
$link_update = $link_base.'/update.php/?id_produto=';
$link_create = $link_base . '/create.php';

$body = require '../parciais/list_objets_html.php';

require '../app.php';
