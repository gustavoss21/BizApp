<?php

require_once 'inc/config.php';
require_once 'inc/api_functions.php';
$data_client = [
    'name' => 'gustavo',
    'telefone' => '123456765432'
];

$data_product = [
    'filter' => ['id_cliente' => 22]
];

// $result = api_request('get_products', 'GET', $data_product);
$result2 = api_request('get_clients','GET',$data_product);

echo '<pre>';
// print_r($result);
print_r($result2);
