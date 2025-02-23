<?php
$allowedRoute = true;

require_once '../inc/config.php';
require_once '../inc/api_functions.php';

session_start();

if (!isset($_GET['id'])) {
    header('Location: index.php/');
}

$id = $_GET['id'];
$params = [
    'filter' => 'id:' . $id
];

$endpoint = 'get-products';
$response = api_request_auth($endpoint, $user, 'GET', $params);

$data_product = is_request_error($response);
$data_product = $data_product[0];
$submit_uri = '/projeto_api/app/produtos/edit.php';

$input_error = isset($_SESSION['input_error']) ? $_SESSION['input_error'] : [];
unset($_SESSION['input_error']);

$message = isset($_SESSION['message']) ? $_SESSION['message'] : [];
unset($_SESSION['message']);

$data = [
    'uri' => $submit_uri,
    'inputs' => [
        'id_product' => ['identifier' => 'id', 'label' => '', 'type' => 'hidden', 'value' => $data_product->id, 'text_error' => ''],
        'produto' => ['identifier' => 'produto', 'label' => 'produto', 'type' => 'text', 'value' => $data_product->produto, 'text_error' => $input_error->produto ?? ''],
        'quantidade' => ['identifier' => 'quantidade', 'label' => 'quantidade', 'type' => 'number', 'value' => $data_product->quantidade, 'text_error' => $input_error->quantidade ?? ''],
    ],
    'elements' => [
        'btn-submit' => ['identifier' => 'submit-form', 'class' => 'input-submit input-element', 'tag_type' => 'button', 'label' => 'Atualizar', 'action' => 'type="submit"'],
        'btn-back' => ['identifier' => 'btn-back', 'tag_type' => 'a', 'class' => 'element-back', 'label' => 'Voltar', 'action' => 'href="../index.php"'],
    ]];

$title = 'Produto';
$subtitle = 'Atualizar produto ' . $data_product->produto;
$body = require '../parciais/form.php';
require '../app.php';
