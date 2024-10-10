<?php

require_once '../inc/config.php';
require_once '../inc/api_functions.php';

session_start();

$endpoint = 'get_products';
$paramenters = [
    'filter' => 'active:true',
    ...$_GET
];

$data = api_request_auth($endpoint, $user, 'GET', $paramenters);
// printDebug($data,true);
$data = is_request_error($data);

$message = isset($_SESSION['message']) ? $_SESSION['message'] : [];
unset($_SESSION['message']);

$title = 'produto';
$subtitle = 'todos os produtos';
$link_base = '/projeto_api/app/produtos';
$link_delete = $link_base . '/confirmation_destroy.php/?id_produto=';
$link_update = $link_base . '/update.php/?id_produto=';
$link_create = $link_base . '/create.php';

$body = require '../parciais/list_objets_html.php';

require '../app.php';
