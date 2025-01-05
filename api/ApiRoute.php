<?php

namespace Api;

if (!isset($allowedRoute)) {
    die('<div style="color:red;">Rota não encontrada</div>');
}

require_once 'inc/Filter.php';
require_once 'inc/Response.php';
require_once 'inc/route_base.php';
require_once 'action_route/User.php';
require_once 'action_route/Cliente.php';
require_once 'action_route/Product.php';
require_once 'action_route/Payment.php';
require_once 'action_route/Price.php';

use Api\inc\Filter;
use Api\inc\Response;
use Api\action_route\Cliente;
use Api\action_route\Product;
use Api\action_route\Payment;
use Api\action_route\User;
use Api\action_route\Price;
use Api\inc\RouteBase;
use Exception;

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
                // 'authorizationOnlyOne',
                'getRequiredMethod'
            ],
            'getOneUser' => [
                'postRequiredMethod'
            ],
            'createUser' => [
                // 'superAuthorizationRequired',
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

    public function createPreference()
    {
        //get client parameters
        $paramsToFilterQuery = $this->params['filter'] ?? '';
        $clients_parameters = $this->getFilter($paramsToFilterQuery);

        //get payment
        $payment = new Payment();
        $payment->setParameter('id', $clients_parameters['id_payment']);
        $paymentStatus = $payment->getPaymentByID();

        //get price
        $price = new Price($payment->getParameter('transaction_amount_id'));
        $price->getPrice();

        if (empty($paymentStatus['data'])) {
            return $this->responseError('payment not found');
        }

        //get user
        $user = new User(id:$payment->getParameter('id_customer'));
        $user->search_user();

        $paymentData = $paymentStatus['data'][0];

        if ($paymentData['status'] === 'approved') {
            $userReponse = $user->getMatchingParameters(['nome', 'email', 'identification_type', 'identification_number']);

            $responseData = [
                'status_payment' => 'approved',
                'id_external_payment' => $paymentData['id_external'],
                'preference_id' => '',
                'user' => $userReponse
            ];

            return $this->responseSuccess($responseData, 'payment already exists');
        }

        $status = $payment->createPreference($user);
        $status['amount'] = $price->getParameter('price');

        return $this->responseSuccess($status, 'preference ok');
    }

    //routes api
    protected function process_payment()
    {
        $user = new User();
        $user_email = $this->params['payer']['email'];
        $user_last_name = $this->params['payer']['last_name'] ?? '';
        $user->setParameter('email', $user_email);
        $user->setParameter('limit', 1);
        $user->search_user();
        $user->setParameter('sobrenome', $user_last_name);

        //set client parameters
        $Payment = new Payment();
        self::setClassParameters($Payment, $this->params);

        if (!$user->getParameter('id')) {
            return $this->responseError('houve um error inesperado no pagamento, verifique os dados do usuário');
        }

        $Payment->setParameter('id_customer', $user->getParameter('id'));

        $Payment->getPaymentUser();

        //get price
        $price = new Price($Payment->getParameter('transaction_amount_id'));
        $price->getPrice();

        if ($price->getParameter('price') !== $Payment->getParameter('transaction_amount')) {
            return $this->responseError('houve um error inesperado no pagamento');
        }

        try {
            if (in_array($Payment->getParameter('payment_method_id'), ['pix', 'bolbradesco'])) {
                $status = $Payment->generatePayment($user);
            } else {
                $status = $Payment->pay($user);
            }
        } catch (Exception $error) {
            return $this->responseError('Houve um erro inesperado, status do error: ' . $error);
        }

        //defines updated payment
        $Payment->setParametersAfterPaymentByCard($status);
        $Payment->setParameter('id_customer', $user->getParameter('id'));
        $Payment->setParameter('expire_in', $this->projectionData(30));

        //update payment
        $Payment->update();

        $statusPayment = $Payment->getParameter('status');

        if (!in_array($statusPayment, ['approved', 'pending'])) {
            return $this->responseError('pagamento não realizado, status: ' . $statusPayment);
        }

        $Payment->setParameter('token', '');
        return $this->responseSuccess($Payment->getParameters(), 'payment ok');
    }

    //routes api
    protected function get_payment_From_api()
    {
        if (!$this->params['id']) {
            return $this->responseError('id do pagamento requisitado');
        }

        //set client parameters
        $Payment = new Payment();
        $Payment->setParameter('id', $this->params['id']);

        $Payment->getPaymentByID();

        try {
            $status = $Payment->getPaymentFromAPI()['data'];
        } catch (Exception $error) {
            return $this->responseError('Houve um erro inesperado, status do error: ' . $error);
        }

        // //defines updated payment
        $Payment->setParametersAfterPaymentByCard($status);
        $Payment->setParameter('expire_in', $this->projectionData(30));

        // //update payment
        // $Payment->update();
        //set client parameters
        $Price = new Price();
        $Price->getPrice();

        $Payment->setParameter('qr_code', $status->point_of_interaction->transaction_data->qr_code);
        $Payment->setParameter('qr_code_base64', $status->point_of_interaction->transaction_data->qr_code_base64);
        $Payment->setParameter('transaction_amount', $Price->getParameter('price'));
        $statusPayment = $Payment->getParameter('status');

        if (!in_array($statusPayment, ['approved', 'pending'])) {
            return $this->responseError('pagamento não realizado, status: ' . $statusPayment);
        }

        $Payment->setParameter('token', '');
        return $this->responseSuccess($Payment->getParameters(), 'payment ok');
    }

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

    protected function getOneUser()
    {
        //get user parameters
        $paramsToFilterQuery = $this->params['filter'] ?? '';
        $userParameters = $this->getFilter($paramsToFilterQuery);

        //set user parameters
        $user = new User();
        self::setClassParameters($user, $userParameters);
        $user->setParameter('limit', 1);

        //get and return users
        $userStatus = $user->search_user();

        if (!$userStatus['data']) {
            return $userStatus;
        }

        $payment = new Payment(id_customer:$user->getParameter('id'));

        $paymentResult = $payment->getPaymentUser();
        if (!$paymentResult) {
            return $this->responseError('user payment not found', [], $user->getparameters());
        }
        $userStatus['data'][] = $paymentResult[0];

        return $userStatus;
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
        $user_status = $user->create_user();

        if ($user_status['error']) {
            return $user_status;
        }

        $user_search = new User();
        $user_search->setParameter('tokken', $user->getParameter('tokken'));

        $userCreated = ($user_search->search_user())['data'][0];

        //get price
        $price = new Price();
        $price->getPrice();

        $payment = new Payment(
            id_customer:$userCreated['id'],
            transaction_amount_id:$price->getParameter('id')
        );

        $payment->create();

        $paymentCreated = $payment->getPaymentUser()[0];

        $user_status['data'] = [];
        $user_status['data'][] = $userCreated;
        $user_status['data'][] = $paymentCreated;

        return $user_status;
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
        if ($isSuperUser) {
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

        if ($isSuperUser) {
            return $this->responseError('it is impossible to change user');
        }

        //destroy user
        return $user->destroy_user();
    }
}
