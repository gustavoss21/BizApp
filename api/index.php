<?php

require_once dirname(__FILE__) . '/inc/config.php';
require_once dirname(__FILE__) . '/inc/api_response.php';
require_once dirname(__FILE__) . '/inc/database.php';
require_once dirname(__FILE__) . '/inc/api_logic.php';

$api_response = new api_response();
define('endpoint', $_REQUEST['endpoint']);

if (!$api_response->check_method($_SERVER['REQUEST_METHOD'])) {
    $api_response->api_request_error('aconteceu um error inesperado');
};

$api_response->set_method($_SERVER['REQUEST_METHOD']);

$api_response->set_endpoint(endpoint);
// $api_response->teste($_REQUEST, $_GET);
$logic_endpoint = new api_logic($_GET, endpoint);
if (!$logic_endpoint->check_endpoint()) {
    $api_response->api_request_error('Endpoint is not exist');
}

$filters = $_REQUEST['filter'] ?? null ;
$get_data_success = $logic_endpoint->{endpoint}($filters);

if (is_null($get_data_success)) {
    $api_response->api_request_error('error a o consultar o banco de dados');
}

$api_response->set_data_endpoint($get_data_success);

$api_response->send_api_status();
