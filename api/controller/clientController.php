<?php

namespace Api\controller;

require_once 'action_route/Cliente.php';
require_once 'inc/Response.php';
require_once 'controller.php';

use Api\action_route\Cliente;
use Api\inc\Response;
use Api\controller\Controller;

use Exception;


class clientController extends Controller{
    use Response;

    public function __construct(private array $clients_parameters) {
    }
    
    public function get_clients()
    {
        //set clients parameters
        $cliente = new Cliente();
        self::setClassParameters($cliente, $this->clients_parameters);

        //get and return clients
        return $cliente->get_clients();
    }

    public function create_client()
    {
        //set client parameters
        $cliente = new Cliente();
        self::setClassParameters($cliente, $this->clients_parameters);

        //avoid duplicate user
        $ClientExist = $cliente->check_client_exists();

        if ($ClientExist) {
            return $this->responseError('email or name is already registered');
        };

        //create client
        return $cliente->create_client();
    }

    public function update_client()
    {
        //set client parameters
        $cliente = new Cliente();
        self::setClassParameters($cliente, $this->clients_parameters);

        //avoid duplicate client
        $ClientExist = $cliente->check_client_exists();

        if ($ClientExist) {
            return $this->responseError('email or name is already registered');
        };

        //update client
        return $cliente->update_client();
    }

    public function destroy_client()
    {
        //set client parameters
        $cliente = new Cliente();
        self::setClassParameters($cliente, $this->clients_parameters);

        //avoid removing already removed client
        $ClientExist = $cliente->check_client_exists();

        if (!$ClientExist) {
            return $this->responseError('client not fund, try again later');
        };

        //destroy client
        return $cliente->destroy_client();
    }

}