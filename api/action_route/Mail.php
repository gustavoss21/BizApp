<?php

namespace Api\action_route;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
use Api\inc\Response;

require_once 'inc/Response.php';
require '../vendor/autoload.php'; // Inclui o autoload do Composer
require_once 'inc/config.php';


class Email{

    use Response;

    private $mail;
    private $to;
    private $name;

    public const EMAIL_VALIDATION = ['url'=>'http://127.0.0.1/projeto_api/admin/email/validation.php','template'=>'validate_email.php'];
    public const RESET_PASSWORD = ['url'=>'http://127.0.0.1/projeto_api/admin/auth/resetpassword.php','template'=>'reset_password.php'];

    public function __construct(       
        $to,
        $name,
    ) {
        $this->mail = new PHPMailer(true);

        // Configurações do servidor SMTP
        // $this->mail->isSMTP();       
        $this->mail->Mailer = "smtp";                               // Define o uso de SMTP
        $this->mail->Host = MAIL_HOST;               // Endereço do servidor SMTP (exemplo: smtp.gmail.com)
        $this->mail->SMTPAuth = true;    
        $this->mail->SMTPSecure = SMTPSECURE;
        $this->mail->Username = MAIL_USERNAME;           // Seu endereço de e-mail SMTP
        $this->mail->Password = MAIL_PASSWORD;                       // Sua senha SMTP
        $this->mail->Port = MAIL_PORT; 
            // Configuração de codificação
        $this->mail->CharSet = 'UTF-8'; // Define a codificação como UTF-8
        $this->mail->Encoding = 'base64'; // Define a codificação do conteúdo
        $this->mail->SMTPOptions = array(
            'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
            )
        ); 

        // Configurações do e-mail
        $this->to = $to;
        $this->name = $name;
        $this->mail->setFrom(MAIL_USERNAME, 'BizApp');   // Remetente
        $this->mail->addAddress($to, $name); // Destinatário        
    }

    /**
     * @param string $subject 
     * @param string $email_settings @category [EMAIL_VALIDATION, EMAIL_RESET_PASSWORD]
     */
    public function setMail($subject , $email_settings,$user_token){
        //criptografar token
        $encrypt = new Encript($user_token);
        $token_encrypted = $encrypt->encryptData();

        // Conteúdo do e-mail
        $this->mail->isHTML(true);
        $this->mail->Subject = $subject;
        $validation_url = $email_settings['url'] . '?token='.$token_encrypted;
        $username = $this->name;
        $this->mail->Body = require  'inc/'.$email_settings['template'];
    }

    /**
     * É válido notar que a função mail() não é apropriada para um grande volume de e-mail em um loop. Esta função abre e fecha um SMTP socket para cada e-mail, o que não é muito eficiente.
     * Para enviar uma grande quantidade de e-mail, veja os pacotes » PEAR::Mail, e » PEAR::Mail_Queue.
     */
    public function send(){
        return ['send_mail_verification'=> $this->mail->send()];
        // return ['send_mail_verification'=> true];
    }

    
}