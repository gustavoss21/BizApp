<?php

namespace Api\controller;

require_once 'action_route/Payment.php';
require_once 'action_route/Price.php';
require_once 'action_route/User.php';
require_once 'action_route/Mail.php';
require_once 'inc/Response.php';
require_once 'inc/Filter.php';
require_once 'controller.php';

use Api\action_route\Payment;
use Api\action_route\Price;
use Api\action_route\User;
use Api\action_route\Email;
use Api\inc\Response;
use Api\inc\Filter;
use Api\controller\Controller;

use Exception;


class PaymentController extends Controller{
    use Response;
    use Filter;

    public function __construct(private $payment_parameters) {
    }
    public function createPreference()
    {
        $filters = $this->payment_parameters['filter'] ?? '';
        $paramenters = $this->getFilter($filters);

        //get payment
        $payment = new Payment();
        $payment->setParameter('id', $paramenters['id_payment']);
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
    public function process_payment()
    {
        $user = new User();
        $user_email = $this->payment_parameters['payer']['email'];
        $user_last_name = $this->payment_parameters['payer']['last_name'] ?? '';
        $user->setParameter('email', $user_email);
        $user->setParameter('limit', 1);
        $user->search_user();
        $user->setParameter('sobrenome', $user_last_name);

        //set client parameters
        $Payment = new Payment();
        self::setClassParameters($Payment, $this->payment_parameters);

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
        if($statusPayment === 'approved'){
            
            $mail = new Email($user->getParameter('email'),$user->getParameter('nome'));
            $mail->setMail('redefina sua senha', Email::RESET_PASSWORD,$user->getParameter('token'));
            $result_email = $mail->send();
        }

        $Payment->setParameter('token', '');
        return $this->responseSuccess($Payment->getParameters(), 'payment ok');
    }

    //routes api
    public function get_payment_From_api()
    {
        if (!$this->payment_parameters['id']) {
            return $this->responseError('id do pagamento requisitado');
        }

        //set client parameters
        $Payment = new Payment();
        $Payment->setParameter('id', $this->payment_parameters['id']);

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
}