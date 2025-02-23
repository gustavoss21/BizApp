<?php

namespace Api\inc;

if(!isset($allowedRoute)){
    die('<div style="color:red;">Rota não encontrada</div>');
}

trait Response
{
    public function responseError($m, array $input_error = [], $data = [])
    {
        $error = ['data' => $data, 'message' => $m, 'input_error' => $input_error, 'error' => true];
        return $error;
    }

    public function responseSuccess($data, $message, array $input_error = [])
    {
        return ['data' => $data, 'message' => $message, 'input_error' => $input_error, 'error' => false];
    }
}
