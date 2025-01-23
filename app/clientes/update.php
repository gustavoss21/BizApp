<?php
$allowedRoute = true;

require_once '../inc/config.php';
require_once '../inc/api_functions.php';

session_start();

if (!isset($_GET['id'])) {
    $_SESSION['message'] = ['msg' => ['houve um error inesperado, verifique os parâmetros de requisição!'], 'color' => 'green', 'type' => 'ERROR'];
    header('Location: index.php/');
}

$id = $_GET['id'];
$params = [
    'filter' => 'id:' . $id
];

$endpoint = 'get-clients';
$response = api_request_auth($endpoint, $user, 'GET', $params);

$data_client = is_request_error($response);
$data_client = $data_client[0];
$submit_uri = '/projeto_api/app/clientes/edit.php';

$input_error = isset($_SESSION['input_error']) ? $_SESSION['input_error'] : [];
unset($_SESSION['input_error']);

$message = isset($_SESSION['message']) ? $_SESSION['message'] : [];
unset($_SESSION['message']);
$data = [
    'uri' => $submit_uri,
    'inputs' => [
        'id' => ['identifier' => 'id', 'label' => '', 'type' => 'hidden', 'value' => $data_client->id, 'text_error' => ''],
        'email' => ['identifier' => 'email', 'label' => 'Email', 'type' => 'email', 'value' => $data_client->email, 'text_error' => $input_error->email ?? ''],
        'telefone' => ['identifier' => 'telefone', 'label' => 'Telefone', 'type' => 'text', 'value' => $data_client->telefone, 'text_error' => $input_error->telefone ?? ''],
    ],
    'elements' => [
        'btn-submit' => ['identifier' => 'submit-form', 'class' => 'input-submit input-element', 'tag_type' => 'button', 'label' => 'Atualizar', 'action' => 'type="submit"'],
        'btn-back' => ['identifier' => 'btn-back', 'tag_type' => 'a', 'class' => 'element-back', 'label' => 'Voltar', 'action' => 'href="../index.php"'],
    ]];

$title = 'cliente';
$subtitle = 'Atualizar cliente ' . $data_client->nome;
$body = require '../parciais/form.php';
require '../app.php';
