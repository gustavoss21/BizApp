<?php

require_once 'inc/config.php';
require_once 'inc/api_functions.php';

session_start();
// session_destroy();
if (!(isset($_SESSION['user']['username']) and $_SESSION['user']['username'])) {
    session_destroy();
    header('Location: auth/login.php');
    exit;
}

if (!(isset($_SESSION['Authorization']) and $_SESSION['Authorization'])) {
    session_destroy();
    header('Location: auth/login.php');
    exit;
}

$message = [];
$endpoint = 'get_users';
// printDebug($_SESSION);
// session_destroy();
$paramers = ['filter' => implode(';', $_GET)];
// printDebug($paramers,true);

$request = api_request($endpoint, 'GET', $paramers);
// printDebug($request,true);
$data = is_request_error($request);

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
}

unset($_SESSION['message']);

// Utilizando a superglobal $_GET com filtro adequado para evitar injeções de código
$filterIsActive = filter_input(INPUT_GET, 'filter', FILTER_SANITIZE_SPECIAL_CHARS) ?? '';

// Definindo a palavra padrão para o filtro ativo
$activeWordFilter = 'all';

// Definindo o array de filtros e suas classes CSS padrão
$filterActive = [
    'all' => '',
    'active' => '',
    'inactive' => ''
];

// Verificando se há filtro e ajustando o filtro ativo conforme o valor recebido
if (!empty($filterIsActive)) {
    $activeWordFilter = ($filterIsActive === 'active:true') ? 'active' : 'inactive';
}

// Atualizando a classe CSS do filtro ativo
$filterActive[$activeWordFilter] = 'class="filter_active"';
$title = 'Usuários';
$subtitle = 'Usuários da API';
$link_base = '/projeto_api/admin/';
$link_delete = $link_base . 'user/confirmation_destroy.php/?id=';
$link_active = $link_base . 'user/active.php/?id=';
$link_update = $link_base . 'user/update.php/?id=';
$link_create = $link_base . 'user/create.php';

$body = require 'parciais/list_objets_html.php';
require 'layout.php';
