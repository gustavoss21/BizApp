<?php

require_once '../inc/config.php';
require_once '../inc/api_functions.php';

session_start();

$_SESSION['message'] = ['msg' => 'não há usuario logando!', 'color' => 'red'];

if (isset($_SESSION['user']['user_id']) && $_SESSION['user']['user_id']) {
    $user = $_SESSION['user'];
    $_SESSION['message'] = ['msg' => 'você fez logout! nos vemos na proxima ' . $user['username'], 'color' => 'green'];

    unset($_SESSION['user']);
}

header('location: /projeto_api/admin');
