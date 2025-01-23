<?php

use Composer\Pcre\Regex;

$allowedRoute = true;

require_once '../inc/config.php';
require_once '../inc/api_functions.php';

session_start();

$endpoint = 'email-validation';

$data['token'] = filter_input(INPUT_GET, 'token', FILTER_SANITIZE_ENCODED);

$response = api_request($endpoint, 'GET', $data);

if ($response->status == 'ERROR') {
    echo '<div style="width:500px;margin:auto;color:red; text-align:center"><h2>Houve um error inesperdo, tente novamente!<h2><a href="http://127.0.0.1/projeto_api/admin/user/status.php?token='.$data['token'].'">voltar para o site</a><div>';
    die;
}

echo '<div style="width:500px;margin:auto;color:blue; text-align:center"><h2>Email validado com sucesso!</h2><a href="http://127.0.0.1/projeto_api/admin/user/status.php?token='.$data['token'].'">voltar para o site</a></div>';
die;

