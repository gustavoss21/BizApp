<?php

function api_request($endpoint, $method = 'GET', $variables = [])
{
    $client = curl_init();

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
    // print_r($response);
    return json_decode($response);
};

function printDebug($data)
{
    echo '<pre>';
    print_r($data);
    die();
}

function is_request_error($data)
{
    $body = '';
    $message = [];

    if (!isset($data->data->data)) {
        $message['error'] = $data->status;
        $message['message'] = $data->message;
        require '../app.php';
        die();
    }
}
