<?php

namespace Api;

require_once 'inc/route_base.php';

use Api\inc\AbstractRoute;


if (!isset($allowedRoute)) {
    die('<div style="color:red;">Rota não encontrada</div>');
}

class ApiRoute extends AbstractRoute
{
    public function setRoutes(){
        $this->routes = [
            'has-super-authorization' => [
                'controller'=>'Api\Controller\UserController',
                'method'=>'has_super_authorization',
                'Required'=>[
                    'getRequiredMethod'
                ],
            ],
            'create-preference' => [
                'controller'=>'Api\Controller\PaymentController',
                'method'=>'createPreference',
                'Required'=>[
                    'getRequiredMethod'
                ],
            ],
            'get-payment-From-api' => [
                'controller'=>'Api\Controller\PaymentController',
                'method'=>'get_payment_From_api',
                'Required'=>[
                    'getRequiredMethod'
                ],
            ],
            'process-payment' => [
                'controller'=>'Api\Controller\PaymentController',
                'method'=>'process_payment',
                'Required'=>[
                    'postRequiredMethod'
                ],
            ],

            'get-clients' => [
                'controller'=>'Api\Controller\ClietController',
                'method'=>'get_clients',
                'Required'=>[
                    'autheticationRequired',
                    'getRequiredMethod'
                ],
            ],
            'create-client' => [
                'controller'=>'Api\Controller\ClietController',
                'method'=>'create_clients',
                'Required'=>[
                    // 'superAuthorizationRequired',
                    'autheticationRequired',
                    'postRequiredMethod'
                ],

            ],
            'update-client' => [
                'controller'=>'Api\Controller\ClietController',
                'method'=>'update_clients',
                'Required'=>[
                    // 'superAuthorizationRequired',
                    'autheticationRequired',
                    'postRequiredMethod'
                ],
            ],
            'destroy-client' => [
                'controller'=>'Api\Controller\ClietController',
                'method'=>'destroy_clients',
                'Required'=>[
                    // 'superAuthorizationRequired',
                    'autheticationRequired',
                    'postRequiredMethod'
                ],
            ],

            'get-products' => [
                'controller'=>'Api\Controller\ProductController',
                'method'=>'get_products',
                'Required'=>[
                    // 'superAuthorizationRequired',
                    'autheticationRequired',
                    'postRequiredMethod'
                ],
            ],
            'create-products' => [
                'controller'=>'Api\Controller\ProductController',
                'method'=>'create_products',
                'Required'=>[
                    // 'superAuthorizationRequired',
                    'autheticationRequired',
                    'postRequiredMethod'
                ],
            ],
            'update-products' => [
                'controller'=>'Api\Controller\ProductController',
                'method'=>'update_products',
                'Required'=>[
                    // 'superAuthorizationRequired',
                    'autheticationRequired',
                    'postRequiredMethod'
                ],
            ],
            'destroy-products' => [
                'controller'=>'Api\Controller\ProductController',
                'method'=>'destroy_products',
                'Required'=>[
                    // 'superAuthorizationRequired',
                    'autheticationRequired',
                    'postRequiredMethod'
                ],
            ],

            'get-users' => [
                'controller'=>'Api\Controller\UserController',
                'method'=>'get_users',
                'Required'=>[
                    'superAuthorizationRequired',
                    // 'authorizationOnlyOne',
                    'getRequiredMethod'
                ],
               
            ],
            'get-one-user' => [
                'controller'=>'Api\Controller\UserController',
                'method'=>'get_one_user',//3333333333333333
                'Required'=>[
                    'postRequiredMethod'
                ],
                
            ],
            'create-user' => [
                'controller'=>'Api\Controller\UserController',
                'method'=>'create_user',
                'Required'=>[
                    // 'superAuthorizationRequired',
                    'postRequiredMethod'
                ],
               
            ],
            'update-user' => [
                'controller'=>'Api\Controller\UserController',
                'method'=>'update_user',
                'Required'=>[
                   'superAuthorizationRequired',
                    'postRequiredMethod'
                ],
                
            ],
            
            'destroy-user' => [
                'controller'=>'Api\Controller\UserController',
                'method'=>'destroy_user',
                'Required'=>[
                   'superAuthorizationRequired',
                    'postRequiredMethod'
                ],   
            ],
            'active-user' => [
                'controller'=>'Api\Controller\UserController',
                'method'=>'active_user',
                'Required'=>[
                   'superAuthorizationRequired',
                    'postRequiredMethod'
                ],   
            ]
        ];
    }
}
