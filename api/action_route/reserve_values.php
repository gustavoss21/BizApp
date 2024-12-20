
<?php
require dirname(__DIR__, 2) . '/vendor/autoload.php';
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Common\RequestOptions;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Client\CardToken\CardTokenClient;


MercadoPagoConfig::setAccessToken('Bearer TEST-3222839361900551-102509-50961e3e4139d32dea7720c15620524e-1446778296');

$client = new PaymentClient();
// $request_options = new RequestOptions();
// $request_options->setCustomHeaders(['X-Idempotency-Key: 0d5020ed-1af6-469c-ae06-c3bec19954bb']);

$valor = (new CardTokenClient())->create([
    "card_expiry"=> "11/25",
    "card_holder"=> "APRO",
    "card_number"=> "4235 6477 2802 5682"
]);
print_r($valor);
exit;
$payment = $client->create(
    [
        'transaction_amount' => 100.0,
        'token' => '123456',
        'description' => 'My product',
        'installments' => 1,
        'payment_method_id' => 'visa',
        'payer' => [
            'email' => 'my.user@example.com',
        ],
        'capture' => false
    ],
    $request_options
);

return $payment;
?>
