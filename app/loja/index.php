<?php

$allowedRoute = true;

require_once '../inc/config.php';
require_once '../inc/api_functions.php';

session_start();

$endpoint = 'createPreference';
$paramenters = [
    'filter' => 'active:true',
    ...$_GET
];

$data = api_request($endpoint, 'GET', $paramenters);
// printDebug($data,true);
$data = is_request_error($data);

$message = isset($_SESSION['message']) ? $_SESSION['message'] : [];
unset($_SESSION['message']);

$title = 'produto';
$subtitle = 'todos os produtos';
$link_base = '/projeto_api/app/produtos';
$link_delete = $link_base . '/confirmation_destroy.php/?id=';
$link_update = $link_base . '/update.php/?id=';
$link_create = $link_base . '/create.php';

$body = require '../parciais/template_loja.php';

require '../app.php';
