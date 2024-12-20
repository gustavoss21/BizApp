<?php

$allowedRoute = true;

require_once '../inc/config.php';
require_once '../inc/api_functions.php';
require_once '../parciais/form.php';

session_start();

$message = '';

$submit_uri = '/projeto_api/admin/user/store.php';
$formMethod = 'POST';

$message = isset($_SESSION['message']) ? $_SESSION['message'] : [];
$input_error = isset($_SESSION['input_error']) ? $_SESSION['input_error'] : [];
$input_values = isset($_SESSION['input_values']) ? $_SESSION['input_values'] : [];

unset($_SESSION['message'], $_SESSION['input_error'], $_SESSION['input_values']);

$title = 'Usuário';
$subtitle = 'novo Usuário';
$idForm = 'creatUseForm';

$form = new SetForm($subtitle,"action='$submit_uri' id='$idForm'",$input_error);
$form->setElement('input',['class'=>'input-element','name'=>'nome', 'id'=>'nome','type'=>'text','value'=>$input_values['name'] ?? ''],'nome');
$form->setElement('input',['class'=>'input-element','name'=>'email', 'id'=>'email','type'=>'email','value'=>$input_values['email'] ?? ''],'email');
$form->setElement('input',['class'=>'input-element','name'=>'fone_number', 'id'=>'fone_number','type'=>'text','value'=>$input_values['fone_number'] ?? ''],'DDD + telefone');
$form->setElement('input',['class'=>'input-element','name'=>'tokken', 'id'=>'tokken','type'=>'text','value'=>$input_values['tokken'] ?? ''],'tokken',['readonly']);
$form->setElement('input',['class'=>'input-element','name'=>'password', 'id'=>'password','type'=>'password','value'=>$input_values['password'] ?? ''],'password',['readonly']);

$identificationType = $form->buildElement('input',['class'=>'identification_type radio','name'=>'identification_type', 'type'=>'radio','value'=>'CPF'],'CPF',['checked']);
$identificationType .= $form->buildElement('input',['class'=>'identification_type radio','name'=>'identification_type', 'id'=>'radio-cnpj','type'=>'radio','value'=>'CNPJ'],'CNPJ');
$form->setElement('input',['class'=>'identification_number input-element','name'=>'identification_number','type'=>'text'],$identificationType);

$form->setElement('button',['id'=>'submit-form','class' => 'input-submit input-element','type'=>'submit'],'Criar');
$form->setElement('a',['id'=>'btn-back','class'=>'element-back','type'=>'text','href'=>'../'],'Voltar');

$body = $form->buildForm();

require '../layout.php';
