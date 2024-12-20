<?php

if(!isset($allowedRoute)){
    die('<div style="color:red;">Rota não encontrada</div>');
}

// open tags
$cont = 0;
$param_hidden = 'id';
$param_link = ['nome', 'produto'];

if (!$data) {
    return $body = '<div style="color:red;text-align:center;">nenhum ' . $title . ' cadastrado';
}
// printDebug($data);
//table head / set icon add
$html = <<<HTML

    <h1 style="text-align: center;color:rgb(148 11 11)">$subtitle 
        <a href="{$link_create}">
            <span class="content-add-icon">
                <div class="add icon-bar-x"></div>
                <div class="add icon-bar-y"></div>
            </span>
        </a>
    </h1>
    <div>
        <div class=content-store>
    HTML;

// main body / set data

foreach ($data as $product) {
    $html .= <<<HTML
    <div class="product-item">
        <h3 style="margin-bottom:5px">$product->produto</h3>
        <span style="color:#00000096">em estoque: $product->quantidade</span>
        <div style="display:flex">
            <a class="btn-buy" href="../checkout/index.php/?filter=produto:$product->id">comprar</a>
            <button class="btn-buy cart" type="btn">adicionar a o carrinho</button>
        </div>
    </div>
    HTML;
}

// close tags
$html .= <<<HTML
        </div>
    </div>
HTML;

return $html;
