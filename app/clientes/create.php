<?php

require_once '../inc/config.php';
require_once '../inc/api_functions.php';
$submit_uri = '/projeto_api/app/clientes/store.php';
$data = [
    'uri' => $submit_uri,
    'inputs' => [
        'nome' => ['identifier'=>'nome','label'=>'digite o um nome ou apelido!','type'=>"text"] , 
        'email' => ['identifier'=>'email','label'=>'digite um email válido!','type'=>"email"],
        'telefone'=> ['identifier'=>'telefone','label'=>'digite um número de telefone!','type'=>"text"],
    ],
    'elements'=>[
        'btn-subtmit'=>['identifier'=>'create-client','class'=>'input-submit input-element','type'=>"button", 'label'=>'Criar', 'action'=>'type="submit"'],
        'btn-back'=>['identifier'=>'btn-back','type'=>"a", 'class'=>'element-back', 'label'=>'Voltar', 'action'=>'href="../clientes"'],
     ]];
$title = 'cliente';
$subtitle = 'novo cliente';
$body = require '../parciais/form.php';

// print_r($body);
require '../app.php';
