<?php

namespace Api\Controller;

require_once 'action_route/User.php';
require_once 'action_route/Payment.php';
require_once 'action_route/Price.php';
require_once 'inc/Response.php';
require_once 'inc/Filter.php';
require_once 'controller.php';

use Api\action_route\User;
use Api\action_route\Payment;
use Api\action_route\Price;
use Api\inc\Response;
use Api\inc\Filter;
use Api\controller\Controller;

use Exception;


class UserController extends Controller{
    use Response;
    use Filter;
    
    public function __construct(private array $user_parameters) {
    }

    public function has_super_authorization(){
        $user = new User();
        self::setClassParameters($user, $this->user_parameters);

        return $user->checkIsSuperUser();
    }
    
    public function get_one_user()
    {
        $filters = $this->user_parameters['filter'] ?? '';
        $paramenters = $this->getFilter($filters);
        
        //set user parameters
        $user = new User();
        self::setClassParameters($user, $paramenters);
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

    public function get_users()
    {
        $filters = $this->payment_parameters['filter'] ?? '';
        $paramenters = $this->getFilter($filters);
        
        //set user parameters
        $user = new User();
        self::setClassParameters($user, $paramenters);

        //get and return users
        return $user->get_users();
    }

    public function create_user()
    {
        //set user parameters
        $user = new User();
        self::setClassParameters($user, $this->user_parameters);

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

    public function update_user()
    {
        //set user parameters
        $user = new User();
        self::setClassParameters($user, $this->user_parameters);

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
        return $user->update_user();
    }

    public function active_user()
    {
        //set user parameters
        $user = new User();
        self::setClassParameters($user, $this->user_parameters);

        //avoid removing already removed user
        $usertExist = $user->checkUserExists();

        if (!$usertExist) {
            return $this->responseError('the user not found, try again later!');
        }

        //destroy user
        return $user->activeUser();
    }

    public function destroy_user()
    {
        //set user parameters
        $user = new User();
        self::setClassParameters($user, $this->user_parameters);

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