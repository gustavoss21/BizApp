<?php

class api_response
{
    private $data;
    private $available_methods = ['GET', 'POST'];

    public function __construct()
    {
        $this->data = [
            'data' => [],
            'method' => null,
            'endpoint' => null,
            'status' => null,
            'message' => null,
            'input_error' => []

        ];
    }

    public function printDebug()
    {
        echo '<pre>';
        print_r($this->data);
        die();
    }

    public function check_method($method)
    {
        return in_array($method, $this->available_methods);
    }

    public function set_method($method)
    {
        $this->data['method'] = $method;
    }

    public function set_endpoint($endpoint)
    {
        $this->data['endpoint'] = $endpoint;
    }

    public function get_endpoint()
    {
        return $this->data['endpoint'];
    }

    public function get_method()
    {
        return $this->data['method'];
    }

    public function api_request_error(string $message = '', array $input_error = [], $debug = true, $data = null)
    {
        if (!$debug) {
            $message = 'hove um error inesperado, verifique os dados de requisição!';
        }
        $this->data['status'] = 'ERROR';
        $this->data['message'] = $message;
        $this->data['input_error'] = $input_error;
        $this->data['data'] = $data;
        $this->send_response();
    }

    public function set_data_endpoint($value)
    {
        $this->data['data'] = $value;
    }

    public function send_api_status()
    {
        $this->data['status'] = 'SUCCESS';
        $this->data['message'] = 'API is running ok';
        $this->send_response();
    }

    public function teste($request, $get)
    {
        $this->data['GET'] = $get;
        $this->data['REQUEST'] = $request;
        $this->send_response();
    }

    public function send_response()
    {
        header('Content-Type: application/json');
        echo json_encode($this->data);
        die(1);
    }
}
