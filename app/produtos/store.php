<?php

require_once '../inc/config.php';
require_once '../inc/api_functions.php';

$endpoint = 'create_products';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: create.php/');
}

$data = api_request($endpoint, 'POST', $_POST);

printDebug($data);

