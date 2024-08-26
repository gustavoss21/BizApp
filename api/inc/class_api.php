<?php


class api_class
{
    private $data;
    private $available_methods = ['GET', 'POST'];

    public function __construct()
    {
        $this->data = [];
    }

    public function check_method($method)
    {
        return in_array($method, $this->available_methods);
    }

    public function set_method($method)
    {
        $this->data['method'] = $method;
    }

    public function api_request_error($message=''){
        $this->data['status'] = 'ERROR';
        $this->data['error_message'] = $message;
        $this->send_response();
    }

    public function send_api_status(){
        $this->data['status'] = 'SUCCESS';
        $this->data['message'] = 'API is running ok';
        $this->send_response();
    }

    public function send_response(){
        header('Content-Type: application/json');
        echo json_encode($this->data);
        die(1);

    }
}

