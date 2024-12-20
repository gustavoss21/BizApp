<?php
require_once dirname(__DIR__,2) . '/vendor/autoload.php';
use MercadoPago\Item;
use MercadoPago\Preference;


// Cria um objeto de preferência
$preference = new Preference();
 
// Cria um item na preferência
$item = new Item();
$item->title = 'Meu produto';
$item->quantity = 1;
$item->unit_price = 75.56;
$preference->items = array($item);
 
// o $preference->purpose = 'wallet_purchase'; permite apenas pagamentos logados
// para permitir pagamentos como guest, você pode omitir essa propriedade
$preference->purpose = 'wallet_purchase';
$preference->create();
?> 