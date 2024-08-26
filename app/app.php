<?php

require_once('inc/config.php');
require_once('inc/api_functions.php');
$data = [
    'name'=> 'gustavo',
    'telefone' => '123456765432'
];

$result = api_request('teste','GET',$data);

echo '<pre>';
print_r($result);