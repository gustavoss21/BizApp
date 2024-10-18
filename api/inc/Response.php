<?php

namespace Api\inc;

trait Response
{
    public static function responseError($m, array $input_error = [], $data = [])
    {
        $error = ['data' => $data, 'message' => $m, 'input_error' => $input_error, 'error' => true];
        return $error;
    }

    public static function response($data, $message, array $input_error = [])
    {
        return ['data' => $data, 'message' => $message, 'input_error' => $input_error, 'error' => false];
    }
}
