<?php
if(!isset($allowedRoute)){
    die('<div style="color:red;">Rota não encontrada</div>');
}
//setting database
define('TOKEN','5414b512bd134ae4c5912e10b647b3df');
define('DB_SERVER','localhost');
define('DB_NAME','db_api');
define('DB_USERNAME','postgres');
define('DB_PASSWORD','991465393gs');
define('DB_CHARSET','utf8');
define('DB_PORT','5432');
define('HOST_API','');

//setting mail
define('MAIL_HOST','smtp.gmail.com');
define('SMTPAUTH','');
define('MAIL_USERNAME','santos.gs708@gmail.com');
define('MAIL_PASSWORD','oiytloudtvucbpmy');
define('SMTPSECURE','tls');
define('MAIL_PORT',587);
