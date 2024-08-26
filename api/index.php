<?php

require_once (dirname(__FILE__) . '/inc/config.php');
require_once (dirname(__FILE__) . '/inc/class_api.php');

$api = new api_class();

$result = $api->send_api_status();

if (!$api->check_method($_SERVER['REQUEST_METHOD'])){
    $api->api_request_error('aconteceu um error inesperado');
};
// $data = [];

// if($_SERVER['REQUEST_METHOD'] == 'GET'){ 
//     $data['data'] = ($_GET);
//     $data['method'] = 'GET';
// }

// if($_SERVER['REQUEST_METHOD'] == 'POST'){ 
//     $data['data'] = ($_POST);
//     $data['method'] = 'POST';
// }


// response($data);


// function response_success(&$data, $message){
//     $data['status'] = 'sucsses';
//     $data['data'] = $message;
// }

// function check_method($method){
//     $accept_methods = ['GET', 'POST'];
//     return in_array($method,$accept_methods,true);
// }


// function response($response_data){
//     header('Content-Type: application/json');
//     echo json_encode($response_data);
// }

