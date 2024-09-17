<?php

require_once '../inc/config.php';
require_once '../inc/api_functions.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    header("Location: index.php/");
}
$endpoint = 'destroy_client';

$request = api_request($endpoint, 'POST', $_POST,true);
printDebug($request);


