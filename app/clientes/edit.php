<?php

require_once '../inc/config.php';
require_once '../inc/api_functions.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    header("Location: index.php/");
}
$endpoint = 'update_client';

$request = api_request($endpoint, 'POST', $_POST);
printDebug($request);