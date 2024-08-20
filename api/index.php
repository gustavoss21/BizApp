<?php

$data = [];

$option = $_GET['option'];

if(isset($option)){

    switch($option){
        case 'valor':
            $data['status'] = 'sucsses';
            $data['data'] = 'dados e mais dados';
            break;

        default:
            $data['status'] = 'error';
    }
}else{
    $data['status'] = 'error';
}

response($data);

function response($response_data){
    header('Content-Type: application/json');
    echo json_encode($response_data);
}