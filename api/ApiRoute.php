<?php

namespace Api;

require_once 'inc/Filter.php';
require_once 'inc/Response.php';
require_once 'inc/route_base.php';
require_once 'action_route/User.php';
require_once 'action_route/Cliente.php';
require_once 'action_route/Product.php';

use Api\inc\Filter;
use Api\inc\Response;
use Api\action_route\Cliente;
use Api\action_route\Product;
use Api\action_route\User;
use Api\inc\RouteBase;

// echo '<pre>';
// print_r(get_declared_classes());
// die;
class ApiRoute extends RouteBase
{
    public function __construct(protected $params, protected $endpoint)
    {
        $this->user = new User();
        $this->requiredRoutePermissions = [
            'get_clients' => [
                'autheticationRequired',
                'getRequiredMethod'
            ],
            'create_client' => [
                // 'superAuthorizationRequired',
                'autheticationRequired',
                'postRequiredMethod'
            ],
            'update_client' => [
                // 'superAuthorizationRequired',
                'autheticationRequired',
                'postRequiredMethod'
            ],
            'destroy_client'=>[
                // 'superAuthorizationRequired',
                'autheticationRequired',
                'postRequiredMethod'
            ],

            'get_products' => [
                // 'superAuthorizationRequired',
                'autheticationRequired',
                'getRequiredMethod'
            ],
            'create_products' => [
                // 'superAuthorizationRequired',
                'autheticationRequired',
                'postRequiredMethod'
            ],
            'update_products' => [
                // 'superAuthorizationRequired',
                'autheticationRequired',
                'postRequiredMethod'
            ],
            'destroy_products'=>[
                // 'superAuthorizationRequired',
                'autheticationRequired',
                'postRequiredMethod'
            ]

        ];
    }
    protected function authenticate()
    {
        // $this->user->authenticate();
    }

    //routes api
    protected function get_clients()
    {
        //get client parameters
        $paramsToFilterQuery = $this->params['filter'] ?? '';
        $clients_parameters = $this->getFilter($paramsToFilterQuery);

        //set clients parameters
        $cliente = new Cliente();
        self::setClassParameters($cliente, $clients_parameters);

        //get and return clients
        return $cliente->get_clients();
    }

    protected function create_client()
    {
        //set client parameters
        $cliente = new Cliente();
        self::setClassParameters($cliente, $this->params);

        //avoid duplicate user
        $responseClient = $cliente->check_client_exists();
        if ($responseClient['error']) {
            return $responseClient;
        };

        //create client
        return $cliente->create_client();
    }

    protected function update_client()
    {
        //set client parameters
        $cliente = new Cliente();
        self::setClassParameters($cliente, $this->params);

        //avoid duplicate client
        $responseClient = $cliente->check_client_exists();
        if ($responseClient['error']) {
            return $responseClient;
        };

        //update client
        return $cliente->update_client();
    }

    protected function destroy_client()
    {
        //set client parameters
        $cliente = new Cliente();
        self::setClassParameters($cliente, $this->params);
        //destroy client
        return $cliente->destroy_client();
    }

    protected function get_products()
    {
        //get client parameters
        $paramsToFilterQuery = $this->params['filter'] ?? '';
        $produtsParameters = $this->getFilter($paramsToFilterQuery);

        //set clients parameters
        $product = new Product();
        self::setClassParameters($product, $produtsParameters);

        //get and return clients
        return $product->get_products();
    }

    protected function create_product()
    {
        //set client parameters
        $product = new Product();
        self::setClassParameters($product, $this->params);

        //avoid duplicate user
        $responseClient = $product->check_product_exists();
        if ($responseClient['error']) {
            return $responseClient;
        };

        //create Product
        return $product->create_product();
    }

    protected function update_product()
    {
        //set client parameters
        $product = new Product();
        self::setClassParameters($product, $this->params);

        //avoid duplicate client
        $productExist = $product->check_product_exists();
        
        if ($productExist) {
            return $this->responseError('Produto já está cadastrado');

        }

        //update client
        return $product->update_product();
    }

    protected function destroy_product()
    {
        //set client parameters
        $product = new Product();
        self::setClassParameters($product, $this->params);

        //destroy client
        return $product->destroy_product();
    }
}
