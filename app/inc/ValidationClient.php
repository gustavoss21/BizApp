<?php

class ValidationClient
{
    public $messageError = [];

    static function ValidateAll($data){

    }

    public function nome($nome){
        if(!isset($nome) and count($nome) < 4){
            $messageError['nome']['tamanho'] = 'O Campo nome precisa ter no minimo 4 caracteres';
        }
    }

    public function telefone(){

    }


}
