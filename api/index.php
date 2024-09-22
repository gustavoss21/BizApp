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
$logic_data = new api_logic($_REQUEST, endpoint);


if (!$logic_data->check_endpoint()) {
    $api_response->api_request_error('Endpoint is not exist');
}

$logic_data->setMethod($api_response->get_method());

$get_data_success = $logic_data->{endpoint}();

if ($get_data_success['error'] ) {
   $api_response->api_request_error($get_data_success['message'],$get_data_success['input_error']);

}

$api_response->set_data_endpoint($get_data_success['data']);

$api_response->send_api_status();
