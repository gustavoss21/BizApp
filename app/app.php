<?php

define('URL_BASE','http://localhost/projeto_api/api/?option=');


echo '<h1>Aplicação</h1>';

$resposta = request_api('valor');

echo '<pre>';
print_r($resposta);


function request_api($option){

    // create a new cURL resource
    $ch = curl_init();
    
    // set URL and other appropriate options
    curl_setopt($ch, CURLOPT_URL, URL_BASE . $option);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);

    // grab URL and pass it to the browser
    $resposta = curl_exec($ch);
    print_r($resposta);
    return json_decode($resposta);
}
