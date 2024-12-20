<?php
$templateUser = <<<HTML
    <div style="padding:20px;">
        <h3 style="margin-bottom:20px;">Resultado</h3>
        <div><strong>nome do usuário:</strong> {$userData->nome}</div>
        <div><strong>token:</strong> {$userData->tokken}</div>
        <div><strong>criado em:</strong> {$userData->criado}</div>
    HTML;

$templatePayment = <<<HTML

    <div style="display: flex; gap: 5px;">
        <p style="border: 1px solid rgb(148 11 11 / 43%);"></p>
        <samp>
            <div><strong>status do cliente:</strong>inativo</div>
            <div><strong>detalhes: </strong>pagamento não realizado</div>
            <a href="../checkout/index.php?id_payment={$paymentData->id}" class="button-custom-yellow">pagar</a>
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
    // data":[{"id":44,"nome":"Gustavo S Souza123242",
    // "tokken":"NxbrQDztiehZHCgo1jI8y2AWPXRFsSEa",
    // "criado":"2024-11-24 23:57:33.851692+05:30","removido":null}]