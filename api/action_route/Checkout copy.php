<?php
namespace Api\action_route;
require_once dirname(__DIR__,2) . '/vendor/autoload.php';
require_once dirname(__DIR__, 1) . '/inc/Response.php';

use Api\inc\Response;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Common\RequestOptions;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Client\CardToken\CardTokenClient;
use MercadoPago\Client\MercadoPagoClient;

// Inicialize o SDK com o Access Token
//https://www.mercadopago.com.br/developers/pt/docs/your-integrations/dashboard
//https://documenter.getpostman.com/view/15366798/2sAXjKasp4#51c4626b-c617-490b-b520-5dcc5ee4ac47
//SDK PHP
//"mercadopago/dx-php:3.0.8"
//https://github.com/mercadopago/sdk-php


class Checkout
{
    public function __construct(
        private $token = '', 
        private $transactionAmount = '',
        private $installments = '',
        private $issuer = '',
        private $identificationType = '',
        private $identificationNumber = '',
        private $paymentMethodId = '',
        private $description = '',
        private $email = ''
        ) {
    } 
    
    /**
     * @return array <p>array with checkout attributes and their value
     */
    public function get_checkout_parameters()
    {
        return get_object_vars($this);
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

        $this->$parameter = trim($value);
    }
    
    public function send()      
    {
        MercadoPagoConfig::setAccessToken('APP_USR-3222839361900551-102509-3124577337ef93b224a580a5e01b755a-1446778296');

        // $
        // $
        // $
        // $issuer
        // $identificationType
        // $identificationNumber
        // $
        // $description
        // $email
        $client = new PaymentClient();
        $request_options = new RequestOptions();
        // $request_options->setCustomHeaders(["X-Idempotency-Key:".'']);
        $createRequest = [
            "additional_info" => [
                "items" => [
                    [
                        "id" => "MLB2907679857",
                        "title" => "Point Mini",
                        "description" => $this->getParameter('description'),
                        "picture_url" => "https://http2.mlstatic.com/resources/frontend/statics/growth-sellers-landings/device-mlb-point-i_medium2x.png",
                        "category_id" => "electronics",
                        "quantity" => 1,
                        "unit_price" => 58,
                        "type" => "electronics",
                        "event_date" => "2023-12-31T09:37:52.000-04:00",
                        "warranty" => false,
                        "category_descriptor" => [
                            "passenger" => [],
                            "route" => []
                        ]
                    ]
                ],
                "payer" => [
                    "first_name" => "Test",
                    "last_name" => "Test",
                    "phone" => [
                        "area_code" => 11,
                        "number" => "987654321"
                    ],
                    "address" => [
                        "street_number" => null
                    ],
                    "shipments" => [
                        "receiver_address" => [
                            "zip_code" => "12312-123",
                            "state_name" => "Rio de Janeiro",
                            "city_name" => "Buzios",
                            "street_name" => "Av das Nacoes Unidas",
                            "street_number" => 3003
                        ],
                        "width" => null,
                        "height" => null
                    ]
                ],
            ],
            "application_fee" => null,
            "binary_mode" => false,
            "campaign_id" => null,
            "capture" => false,
            "coupon_amount" => null,
            "description" => "Payment for product",
            "differential_pricing_id" => null,
            "external_reference" => "MP0001",
            "installments" => $this->getParameter('installments'),
            "metadata" => null,
            "payer" => [
                "entity_type" => "individual",
                "type" => "customer",
                "email" => "test_user_123@testuser.com",
                "identification" => [
                    "type" => "CPF",
                    "number" => "95749019047"
                ]
            ],
            "payment_method_id" => $this->getParameter('paymentMethodId'),
            "token" => $this->getParameter('token'),
            "transaction_amount" => $this->getParameter('transactionAmount'),
        ];
        return $client->create($createRequest, $request_options);
    }
}
