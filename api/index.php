<?php
$allowedRoute = true;

require_once dirname(__FILE__) . '/inc/config.php';
require_once dirname(__FILE__) . '/inc/api_response.php';
require_once dirname(__FILE__) . '/apiRoute.php';

use Api\ApiRoute;

$api_response = new api_response();

define('endpoint', $_REQUEST['endpoint']);

if (!$api_response->check_method($_SERVER['REQUEST_METHOD'])) {
    $api_response->api_request_error('aconteceu um error inesperado');
};

$api_response->set_method($_SERVER['REQUEST_METHOD']);

$api_response->set_endpoint(endpoint);
$logic_data = new apiRoute($_REQUEST, endpoint);

if (!$logic_data->check_endpoint()) {
    $api_response->api_request_error('Endpoint is not exist');
}

// set user
if (isset($_SERVER['PHP_AUTH_USER'])) {
    $logic_data->setUser($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
}

$logic_data->setMethod($api_response->get_method());

//call method endpoint
$data_response = $logic_data->route(endpoint);

if ($data_response['error']) {
    $api_response->api_request_error($data_response['message'], $data_response['input_error']);
}

$api_response->set_data_endpoint($data_response['data']);

$api_response->send_api_status();
