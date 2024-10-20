<?php

namespace Api;

if(!isset($allowedRoute)){
    die('<div style="color:red;">Rota não encontrada</div>');
}

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
            'destroy_client' => [
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
            'destroy_products' => [
                // 'superAuthorizationRequired',
                'autheticationRequired',
                'postRequiredMethod'
            ],

            'getUsers' => [
                'superAuthorizationRequired',
                'getRequiredMethod'
            ],
            'createUser' => [
                'superAuthorizationRequired',
                'postRequiredMethod'
            ],
            'updateUser' => [
                'superAuthorizationRequired', 
                'postRequiredMethod'
            ],
            'destroyUser' => [
                'superAuthorizationRequired',
                'postRequiredMethod'
            ]

        ];
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
        $ClientExist = $cliente->check_client_exists();

        if ($ClientExist) {
            return Response::responseError('email or name is already registered');
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
        $ClientExist = $cliente->check_client_exists();
        
        if ($ClientExist) {
            return Response::responseError('email or name is already registered');
        };

        //update client
        return $cliente->update_client();
    }

    protected function destroy_client()
    {
        //set client parameters
        $cliente = new Cliente();
        self::setClassParameters($cliente, $this->params);
        
        //avoid removing already removed client
        $ClientExist = $cliente->check_client_exists();

        if (!$ClientExist) {
            return Response::responseError('client not fund, try again later');
        };
        
        //destroy client
        return $cliente->destroy_client();
    }

    protected function get_products()
    {
        //get product parameters
        $paramsToFilterQuery = $this->params['filter'] ?? '';
        $produtsParameters = $this->getFilter($paramsToFilterQuery);

        //set products parameters
        $product = new Product();
        self::setClassParameters($product, $produtsParameters);

        //get and return products
        return $product->get_products();
    }

    protected function create_product()
    {
        //set product parameters
        $product = new Product();
        self::setClassParameters($product, $this->params);

        //avoid duplicate product
        $ProductExist = $product->checkProductExists();

        if ($ProductExist) {
            return Response::responseError('the product is already registered');
        };

        //create Product
        return $product->create_product();
    }

    protected function update_product()
    {
        //set product parameters
        $product = new Product();
        self::setClassParameters($product, $this->params);

        //avoid duplicate product
        $ProductExist = $product->checkProductExists();

        if ($ProductExist) {
            return Response::responseError('the product is already registered');
        };

        //update product
        return $product->update_product();
    }

    protected function destroy_product()
    {
        //set product parameters
        $product = new Product();
        self::setClassParameters($product, $this->params);

        //avoid removing already removed product
        $productExist = $product->checkProductExists();

        if (!$productExist) {
            return $this->responseError('the product not found, try again later!');
        }

        //destroy product
        return $product->destroy_product();
    }

    protected function getUsers()
    {
        //get user parameters
        $paramsToFilterQuery = $this->params['filter'] ?? '';
        $produtsParameters = $this->getFilter($paramsToFilterQuery);

        //set user parameters
        $user = new User();
        self::setClassParameters($user, $produtsParameters);

        //get and return users
        return $user->get_users();
    }

    protected function createUser()
    {
        //set user parameters
        $user = new User();
        self::setClassParameters($user, $this->params);

        //avoid duplicate user
        $userExists = $user->checkUserExists();

        if ($userExists) {
            return Response::responseError('the user is already registered');
        };

        //create User
        return $user->create_user();
    }

    protected function updateUser()
    {
        //set user parameters
        $user = new User();
        self::setClassParameters($user, $this->params);

        //avoid duplicate user
        $userExist = $user->checkUserExists();
        if ($userExist) {
            return $this->responseError('the user is already registed');
        }
        
        $isSuperUser = $user->checkIsSuperUser();
        if($isSuperUser){
            return $this->responseError('it is impossible to change user');
        }

        //update user
        return $user->updateUser();
    }

    protected function activeUser()
    {
        //set user parameters
        $user = new User();
        self::setClassParameters($user, $this->params);
        
        //avoid removing already removed user
        $usertExist = $user->checkUserExists();

        if (!$usertExist) {
            return $this->responseError('the user not found, try again later!');
        }

        //destroy user
        return $user->activeUser();
    }

    protected function destroyUser()
    {
        //set user parameters
        $user = new User();
        self::setClassParameters($user, $this->params);

        //avoid removing already removed user
        $usertExist = $user->checkUserExists();

        if (!$usertExist) {
            return $this->responseError('the user not found, try again later!');
        }

        // avoid deactivate super user
        $isSuperUser = $user->checkIsSuperUser();
        
        if($isSuperUser){
            return $this->responseError('it is impossible to change user');
        }

        //destroy user
        return $user->destroy_user();
    }
}
