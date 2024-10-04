<?php

function api_request($endpoint, $method = 'GET', $variables = [], $debug = false)
{
$cred = $_SESSION["Authorization"]; 
    // return base64_decode($cred);
    $headers = [
        'Authorization: Basic ' . $cred
    ];
    // return base64_decode($cred);
    
    $client = curl_init();
    curl_setopt($client, CURLOPT_HTTPHEADER, $headers);

    curl_setopt($client, CURLOPT_RETURNTRANSFER, true);

    $url = API_BASE_URL;

    if ($method == 'GET') {
        $url .= '?endpoint=' . $endpoint;
        if (!empty($variables)) {
            $url .= '&' . http_build_query($variables);
        }
    }

    if ($method == 'POST') {
        $variables = array_merge(['endpoint' => $endpoint], $variables);
        curl_setopt($client, CURLOPT_POSTFIELDS, $variables);
    }

    curl_setopt($client, CURLOPT_URL, $url);

    $response = curl_exec($client);
    // return curl_multi_getcontent($client);

    if ($debug) {
        printDebug($response, true);
    }

    return json_decode($response);
};

function api_request_auth($endpoint, array $user, $method = 'GET', $variables = [])
{
    // return [$endpoint, $method, $variables, $user, $debug ];

    $credenciaisBase64 = base64_encode("{$user['name']}:{$user['password']}");
    $url = API_BASE_URL;

    $headers = [
        'Authorization: Basic ' . $credenciaisBase64
    ];
    $client = curl_init();
    curl_setopt($client, CURLOPT_RETURNTRANSFER, true);

    if ($method == 'GET') {
        $url .= '?endpoint=' . $endpoint;
        if (!empty($variables)) {
            $url .= '&' . http_build_query($variables);
        }
    }

    if ($method == 'POST') {
        $variables = array_merge(['endpoint' => $endpoint], $variables);

        curl_setopt($client, CURLOPT_POSTFIELDS, $variables);
    }

    curl_setopt($client, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($client, CURLOPT_URL, $url);

    $response = curl_exec($client);

    if (curl_errno($client)) {
        throw new Exception(curl_error($client));
    }

    curl_close($client);
    // printDebug($response, true);
    // printDebug(['endp'=>$endpoint,'user'=> $user, 'metho'=>$method,'varia'=> $variables],true);
    return json_decode($response);
};

function printDebug($data, $break = false)
{
    echo '<pre>';
    print_r($data);
    if ($break) {
        die();
    }
}

function is_request_error($request)
{
    $body = '';
    $message = [];

    $request = (object) $request;

    if (!isset($request->data) || $request->status == 'ERROR') {
        $message['color'] = $request->status;
        $message['msg'] = $request->message;
        require 'layout.php';
        die();
    }

    return $request->data;
}