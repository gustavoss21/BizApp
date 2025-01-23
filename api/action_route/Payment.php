<?php

namespace Api\action_route;

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';
require_once dirname(__DIR__, 1) . '/inc/Response.php';
require_once dirname(__FILE__, 2) . '/inc/Validation.php';
require_once dirname(__FILE__, 2) . '/inc/database.php';
require_once dirname(__FILE__, 2) . '/inc/Filter.php';
require_once 'User.php';

// require_once dirname(__DIR__, 1) . '/inc/preference.php';
use Api\action_route\User;

use MercadoPago\SDK;
use MercadoPago\Item;
use MercadoPago\Preference;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\MercadoPagoClient;
use MercadoPago\Client\Common\RequestOptions;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Client\CardToken\CardTokenClient;
use MercadoPago\Client\Preference\PreferenceClient;
// API
use Api\inc\Response;
use Api\inc\Validation;
use Api\inc\database;
use Api\inc\Filter;

// Inicialize o SDK com o Access Token
//https://www.mercadopago.com.br/developers/pt/docs/your-integrations/dashboard
//https://documenter.getpostman.com/view/15366798/2sAXjKasp4#51c4626b-c617-490b-b520-5dcc5ee4ac47
//SDK PHP
//"mercadopago/dx-php:3.0.8"
//https://github.com/mercadopago/sdk-php

// live_mode:true

class Payment
{
    use Validation;
    use Filter;
    use Response;

    public function __construct(
        private $id = 0,
        private $id_external = 0,
        private $id_customer = 0,
        private $status = 'pendente',
        private $token = '',
        private $transaction_amount = '',
        private $transaction_amount_id = '',
        private $installments = '',
        private $issuer_id = '',
        private $payment_method_id = '',
        private $payment_type_id = '',
        private $description = '',
        private $expire_in = '',
        private $card_holder_name = '',
        private $date_approved = '',
        private $card_last_fourt_digits = 0000,
        private $qr_code = '',
        private $qr_code_base64 = ''
    ) {
    }

    /**
     * @return array <p>array with checkout attributes and their value
     */
    public function getAllParameters()
    {
        return get_object_vars($this);
    }

    public function getParameters()
    {
        return  array_filter($this->getAllParameters(), fn ($value) => !empty($value));
    }

    public function getParameter($parameter)
    {
        if (!isset($this->$parameter)) {
            return;
        }
        return $this->$parameter;
    }

    public function setParameter($parameter, $value)
    {
        if (!isset($this->{$parameter})) {
            return;
        }
        if(is_null($value)){
            $this->$parameter = $value;
            return;
        }

        $this->$parameter = trim($value);
    }

    public function setParametersAfterPaymentByCard($status){
        $this->setParameter('transaction_amount', '');
        $this->setParameter('id_external', $status->id);
        $this->setParameter('status', $status->status);
        $this->setParameter('updated_at', $status->date_last_updated);
        $this->setParameter('issuer_id', '');
        $this->setParameter('date_approved', $status->date_approved);
        $this->setParameter('card_last_fourt_digits', $status->card->last_four_digits ?? 0);
        $this->setParameter('card_holder_name', $status->card->cardholder->name ?? '');
        $this->setParameter('payment_type_id', $status->payment_type_id);
        $this->setParameter('id_external', $status->id);
    }

    public function setParametersAfterPaymentByPix($status){
        $this->setParameter('transaction_amount', '');
        $this->setParameter('id_external', $status->id);
        $this->setParameter('status', $status->status);
        $this->setParameter('updated_at', $status->date_last_updated);
        $this->setParameter('issuer_id', '');
        $this->setParameter('date_approved', $status->date_approved);
        $this->setParameter('payment_type_id', $status->payment_type_id);
        $this->setParameter('id_external', $status->id);
    }

    public function pay(User $payer)
    {
        MercadoPagoConfig::setAccessToken('TEST-3222839361900551-102509-50961e3e4139d32dea7720c15620524e-1446778296');
        $client = new PaymentClient();
        $request_options = new RequestOptions();
        // $request_options->setCustomHeaders(['X-Idempotency-Key: key_6726109ac887c0.91631784']);
        $data_pay = [
            'transaction_amount' => (float) $this->getParameter('transaction_amount'),
            'token' => $this->getParameter('token'),
            'description' => $this->getParameter('description'),
            'installments' => (int) $this->getParameter('installments'),
            'payment_method_id' => $this->getParameter('payment_method_id'),
            'issuer_id' => $this->getParameter('issuer_id'),
            'payer' => [
                'email' => $payer->getParameter('email'),
                'identification' => [
                    'type' => $payer->getParameter('identification_type'),
                    'number' => $payer->getParameter('identification_number')
                ]
            ]
        ];

        $payment = $client->create($data_pay, $request_options);

        return $payment;
    }

    public function generatePayment(User $payer){
        MercadoPagoConfig::setAccessToken('TEST-3222839361900551-102509-50961e3e4139d32dea7720c15620524e-1446778296');
        $client = new PaymentClient();
        $request_options = new RequestOptions();

        $createRequest = [
            // "installments"=>1,
            "additional_info"=> [
                "items"=> [
                [
                    "id"=>$payer->getParameter('id'),
                    "title"=> 'usuário '. $payer->getParameter('id'),
                    "description"=> "buy user for api access",
                    "picture_url"=> "https://http2.mlstatic.com/resources/frontend/statics/growth-sellers-landings/device-mlb-point-i_medium2x.png",
                    "category_id"=> "electronics",
                    "quantity"=> 1,
                    "unit_price"=> $this->getParameter('transaction_amount'),
                    "type"=> "electronics",
                ]
                ],
                "payer"=> [
                    "first_name"=> $payer->getParameter('nome'),
                    "last_name"=> "indefinido",
                    "phone"=> [
                        "area_code"=> $payer->getParameter('fone_area_code'),
                        "number"=> $payer->getParameter('fone_number')
                    ]
                ]
            ],
            "binary_mode" => false,
            "description" => "get qrcode for paymen",
            "external_reference" => $this->getParameter('id'),
            "payer" => [
                "entity_type" => "individual",
                "type" => "customer",
                "first_name"=> $payer->getParameter('nome'),
                "last_name"=> 'aaaaaa',
                "email" => $payer->getParameter('email'),
                "identification" => [
                    "type" => $payer->getParameter('identification_type'),
                    "number" => $payer->getParameter('identification_number')
                ]
            ],
            "callback_url"=>"http://127.0.0.1/projeto_api/admin/checkout/pix.php",
            "payment_method_id" => $this->getParameter('payment_method_id'),
            "transaction_amount" => (float) $this->getParameter('transaction_amount'),
        ];

        $response = $client->create($createRequest, $request_options);
        return $response;
    }

    public function createPreference($user)
    {

        MercadoPagoConfig::setAccessToken('TEST-3222839361900551-102509-50961e3e4139d32dea7720c15620524e-1446778296');

        $client = new PreferenceClient();
        $dataPreference = [
            'back_urls' => [
                'success' => 'https://test.com/success',
                'failure' => 'https://test.com/failure',
                'pending' => 'https://test.com/pending'
            ],
            'differential_pricing' => [
                'id' => 1,
            ],
            'expires' => false,
            'items' => [
                [
                    'id' => $user->getParameter('id'),
                    'title' => $user->getParameter('token'),
                    'description' => 'usuario da api',
                    'picture_url' => 'https://www.myapp.com/myimage.jpg',
                    'category_id' => 'user_api',
                    'quantity' => 1,
                    'currency_id' => 'BRL',
                    'unit_price' => 100
                ]
            ],
            'marketplace_fee' => 0,
            'payer' => [
                'name' => $user->getParameter('token'),
                'username' => 'User',
                'email' => $user->getParameter('email'),
                'phone' => [
                    'area_code' => $user->getParameter('fone_area_code'),
                    'number' => $user->getParameter('fone_number')
                ],
                'identification' => [
                    'type' => $user->getParameter('identification_type'),
                    'number' => $user->getParameter('identification_number')
                ],
            ],
            'additional_info' => 'Discount: 12.00',
            'auto_return' => 'all',
            'binary_mode' => true,
            'external_reference' => $this->getParameter('id'),
            'marketplace' => 'none',
            'notification_url' => 'https://notificationurl.com',
            'operation_type' => 'regular_payment',
            'payment_methods' => [
                'default_payment_method_id' => 'master',
                'excluded_payment_types' => [
                    [
                        'id' => 'visa'
                    ]
                ],
                'excluded_payment_methods' => [
                    [
                        'id' => ''
                    ]
                ],
                'installments' => 5,
                'default_installments' => 1
            ],
            'shipments' => [
                'mode' => 'custom',
                'local_pickup' => false,
                'default_shipping_method' => null,
                'free_methods' => [
                    [
                        'id' => 1
                    ]
                ],
                'cost' => 10,
                'free_shipping' => false,
            ],
            'statement_descriptor' => 'compra de usuario na api',
        ];

        $preference = $client->create($dataPreference);
        $userReponse = $user->getMatchingParameters(['nome','email','identification_type','identification_number']);

        return [
            'status_payment'=>'',
            'preference_id'=>$preference->id,
            'user'=>$userReponse
        ];
    }

    public function getPaymentByID()
    {
        $filters = $this->getAllParameters();
        $queryBase = 'select id, id_customer,id_external, transaction_amount_id, status, created_at, expirate_at from payment';
        $query = $queryBase;
        $accepted_filters = [
            'id' => ['param' => 'id = :id', 'operator' => ' and ', 'exclusive' => false],
        ];

        [$filter_query,$queryParameters] = self::setQueryFilterSelect($filters, $accepted_filters);

        if (!empty($filter_query)) {
            $query = $queryBase . ' where ' . $filter_query;
        }

        $conection = new database();
        $payment = $conection->EXE_QUERY($query, $queryParameters);

        if (count($payment) > 0) {
            //set new parameters
            foreach ($payment[0] as $payKey => $payValue) {
                $this->setParameter($payKey, $payValue);
            };
        }

        return $this->responseSuccess($payment, 'seach payment');
    }

    public function getPaymentFromAPI()
    {
        MercadoPagoConfig::setAccessToken('TEST-3222839361900551-102509-50961e3e4139d32dea7720c15620524e-1446778296');        
        $client = new PaymentClient();
        $id_payment = $this->getParameter('id_external');
        $payment = $client->get($id_payment);

        return $this->responseSuccess($payment, 'seach payment');
    }

    public function getPaymentUser()
    {
        $filters = $this->getAllParameters();

        $queryBase = 'select id, id_customer,id_external, status, created_at, expire_in from payment';
        $query = $queryBase;
        $accepted_filters = [
            'id_customer' => ['param' => 'id_customer = :id_customer', 'operator' => ' and ', 'exclusive' => false],
        ];

        [$filter_query,$queryParameters] = self::setQueryFilterSelect($filters, $accepted_filters);

        if (!empty($filter_query)) {
            $query = $queryBase . ' where ' . $filter_query;
        }

        $query .= ' order by created_at desc limit 1';

        $conection = new database();
        $payment = $conection->EXE_QUERY($query, $queryParameters);

        return $payment ?? [];
    }

    public function create()
    {
        //inputs required
        $params_required = ['id_customer' => ['int'], 'status' => ['not_null', ],'transaction_amount_id' => ['int']];

        //checks that the client parameters are set
        $paymentParameter = $this->getParameters();
        $paymentStatus = $this->issetParamasValidation($params_required, $paymentParameter);

        if (!$paymentStatus['valid']) {
            return $this->responseError('existem parâmetros inválidos', $paymentStatus['erros']);
        }

        // commit query
        $paramsToQuery = self::setQueryParams($paymentStatus['data']);
        $connection = new database();
        $query = 'insert into payment (id_customer, transaction_amount_id, status, created_at, updated_at) values(:id_customer, :transaction_amount_id, :status,now(),now())';
        $result = $connection->EXE_NON_QUERY($query, $paramsToQuery);

        return self::responseSuccess($result, 'inserction success');
    }

    public function update()
    {
        $connection = new database();

        //get defined payment parameters
        $paymentParameter = $this->getParameters();

        //set query
        $partialQuery = self::setQueryInsert($paymentParameter);
        $query = "update payment set {$partialQuery}";

        //parameters go to the WHERE clause
        $paramsToQuery = self::setQueryParams($paymentParameter);

        $id_customer = $this->getParameter('id_customer');

        if(empty($id_customer)){
            return $this->responseError('id_customer not founded');
        }

        $query .= ' where id_customer = :id_customer';

        // commit query
        $result = $connection->EXE_NON_QUERY($query, $paramsToQuery);

        return self::responseSuccess($result, 'inserction success');
    }
}
