<?php

if(!isset($allowedRoute)){
    die('<div style="color:red;">Rota não encontrada</div>');
}

$templateUser = <<<HTML
    <div style="padding:20px;">
        <h3 style="margin-bottom:20px;">Resultado</h3>
        <div><strong>nome do usuário:</strong> {$userData->nome}</div>
        <div><strong>token:</strong> {$userData->token}</div>
        <input id="user-token" value="{$userData->token}" hidden>
        <div><strong>criado em:</strong> {$userData->criado}</div>      
    HTML;

$tag_pay = "<a id='pay' href='../checkout/index.php?id_payment={$paymentData->id}' class='button-custom-yellow'>pagar</a>";
$tag_email = "<div><strong>email:</strong> {$userData->email}</div> ";

if(!$userData->email_is_valid){
    $templateUser .= 
        <<<HTML
            <div>
                <span 
                    style="
                        margin: 0 5px 0 1px;
                        background-color: #d20d0d;
                        border-radius: 26px;
                        padding: 0px 9px;
                    ">
                </span>
                <strong>email:</strong> 
                {$userData->email}
            </div> 
            <div style="color: red">enviamos um email, valide seu email! <a id="resend-email" href=""> reenviar!</a></div>

        HTML
    ;
    $tag_pay = "<a id='pay' class='button-custom-yellow' aria-disabled='true'>pagar</a>";
}

$templatePayment = $tag_email . <<<HTML

    <div style="display: flex; gap: 5px;">
        <p style="border: 1px solid rgb(148 11 11 / 43%);"></p>
        <samp>
            <div><strong>status do cliente:</strong>inativo</div>
            <div><strong>detalhes: </strong>pagamento não realizado</div>
            $tag_pay
        </samp>
    </div>
</div>
HTML;

if($paymentData->status === 'approved'){
    $templatePayment = <<<HTML
        <div style="display: flex; gap: 5px;">
            <p style="border: 1px solid rgb(148 11 11 / 43%);"></p>
            <samp>
                <div class="status-user" style="margin:0000"><strong>status do cliente:</strong><span id="signal-green"></span>ativo</div>
            </samp>
        </div>
    </div>
    HTML;
}

return $templateUser.$templatePayment;