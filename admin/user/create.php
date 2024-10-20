<?php

require_once '../inc/config.php';
require_once '../inc/api_functions.php';

session_start();

$message = '';
$endpoint = 'superAuthorizationRequired';
$response = api_request($endpoint,'GET');
// printDebug($response, true);
if ($response->status == 'ERROR') {
    $_SESSION['message'] = ['msg' => $response->message, 'color' => 'red'];
    $_SESSION['input_error'] = $response->input_error;
    $_SESSION['input_values'] = $_POST;
    header('location: ../auth/login.php');
    exit;
}

$submit_uri = '/projeto_api/admin/user/store.php';

$message = isset($_SESSION['message']) ? $_SESSION['message'] : [];
unset($_SESSION['message']);

$input_error = isset($_SESSION['input_error']) ? $_SESSION['input_error'] : [];
unset($_SESSION['input_error']);

$input_values = isset($_SESSION['input_values']) ? $_SESSION['input_values'] : [];
unset($_SESSION['input_values']);

$char_token = 'AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz123456789';

$palavraAleatoria = fn ($caracteres, $tamanho) => substr(str_shuffle($caracteres), 0, $tamanho);

$data = [
    'uri' => $submit_uri,
    'inputs' => [
        'nome' => ['identifier' => 'nome', 'label' => 'nome', 'type' => 'text', 'value' => $input_values['nome'] ?? '', 'text_error' => $input_error->nome ?? '', 'other_params' => 'required'],
        'tokken' => ['identifier' => 'tokken', 'label' => 'tokken | username', 'type' => 'text', 'value' => $palavraAleatoria($char_token, 32), 'text_error' => $input_error->tokken ?? '', 'other_params' => 'readonly required'],
        'password' => ['identifier' => 'password', 'label' => 'senha', 'type' => 'text', 'value' => $palavraAleatoria($char_token, 32), 'text_error' => $input_error->password ?? '', 'other_params' => 'readonly required'],
    ],
    'elements' => [
        'btn-submit' => ['identifier' => 'submit-form', 'class' => 'input-submit input-element', 'tag_type' => 'button', 'label' => 'Criar', 'action' => 'type="submit"'],
        'btn-back' => ['identifier' => 'btn-back', 'tag_type' => 'a', 'class' => 'element-back', 'label' => 'Voltar', 'action' => 'href="clientes/"'],
    ]];
$title = 'Usuário';
$subtitle = 'novo Usuário';
$body = require '../parciais/form.php';

require '../layout.php';
