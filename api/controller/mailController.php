<?php

namespace Api\Controller;

require_once 'action_route/Mail.php';
require_once 'action_route/Mail.php';
require_once 'action_route/User.php';
require_once 'action_route/Encryption.php';

require_once 'inc/Response.php';
require_once 'inc/Filter.php';
require_once 'controller.php';

use Api\action_route\User;
use Api\action_route\Encript;
use Api\action_route\Email;
use Api\inc\Response;
use Api\inc\Filter;
use Api\controller\Controller;

use Exception;


class MailController extends Controller{
    use Response;
    use Filter;
    
    public function __construct(private array $mail_parameters) {
    }

    public function send_email_validation()
    {
        //set user parameters
        $user = new User();
        $user_token = $this->mail_parameters['token'];

        $user->setParameter('token',$user_token);
        $user->setParameter('limit', 1);


        //get and return users
        $userStatus = $user->search_user();

        if (!$userStatus['data']) {
            return $userStatus;
        }

        $mail = new Email($user->getParameter('email'),$user->getParameter('nome'));
        $mail->setMail('validar email',Email::EMAIL_VALIDATION,$user_token);
        $result = $mail->send();

        return $this->responseSuccess($result,'send mail ok');
    }

    public function email_validation(){
        $encrypt = new Encript($this->mail_parameters['token']);
        $token = $encrypt->decryptData();

        $user = new User(token:$token,email_is_valid:true);
        return $user->email_validation();
    }


}