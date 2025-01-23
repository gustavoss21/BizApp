<?php
namespace Api\action_route;

$allowedRoute = true;

require_once 'inc/config.php';

class Encript{
    public function __construct(private string $data) {
    }

    // Função para criptografar os dados
function encryptData() {
    // Define o algoritmo de criptografia
    $cipher = 'AES-256-CBC';

    // Gera um vetor de inicialização (IV)
    $ivLength = openssl_cipher_iv_length($cipher);
    $iv = random_bytes($ivLength);

    // Criptografa os dados
    $encryptedData = openssl_encrypt($this->data, $cipher, TOKEN, 0, $iv);

    // Retorna os dados criptografados junto com o IV (em base64 para facilitar o armazenamento)
    return base64_encode($iv . $encryptedData);
}

// Função para descriptografar os dados
function decryptData() {
    // Define o algoritmo de criptografia
    $cipher = 'AES-256-CBC';

    // Decodifica os dados base64
    $data = base64_decode($this->data);

    // Extrai o IV e os dados criptografados
    $ivLength = openssl_cipher_iv_length($cipher);
    $iv = substr($data, 0, $ivLength);
    $data = substr($data, $ivLength);

    // Descriptografa os dados
    return openssl_decrypt($data, $cipher, TOKEN, 0, $iv);
}   
}