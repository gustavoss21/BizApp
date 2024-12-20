<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>tokken</title>
    <link rel="stylesheet" href="/projeto_api/app/assets/css/style.css">
    <link rel="stylesheet" href="/projeto_api/app/assets/css/style_cliente.css">
    <link rel="stylesheet" href="/projeto_api/app/assets/css/style_form.css">
</head>
<body>
  <style>
    #form-checkout {
      display: flex;
      flex-direction: column;
      max-width: 600px;
    }

    .container {
      height: 18px;
      display: inline-block;
      border: 1px solid rgb(118, 118, 118);
      border-radius: 2px;
      padding: 1px 2px;
    }
  </style>
  <form id="form-checkout" action="process.php" method="POST">
    <div id="form-checkout__cardNumber" class="container"></div>
    <div id="form-checkout__expirationDate" class="container"></div>
    <div id="form-checkout__securityCode" class="container"></div>
    <input type="text" id="form-checkout__cardholderName" placeholder="Titular do cartão" />
    <select id="form-checkout__issuer" name="issuer">
      <option value="" disabled selected>Banco emissor</option>
    </select>
    <select id="form-checkout__installments" name="installments">
      <option value="" disabled selected>Parcelas</option>
    </select>
    <select id="form-checkout__identificationType" name="identificationType">
      <option value="" disabled selected>Tipo de documento</option>
    </select>
    <input type="text" id="form-checkout__identificationNumber" name="identificationNumber" placeholder="Número do documento" />
    <input type="email" id="form-checkout__email" name="email" placeholder="E-mail" />

    <input id="token" name="token" type="hidden">
    <input id="paymentMethodId" name="paymentMethodId" type="hidden">
    <input id="transactionAmount" name="transactionAmount" type="hidden" value="100">
    <input id="description" name="description" type="hidden" value="Nome do Produto">

    <button type="submit" id="form-checkout__submit">Pagar</button>
  </form>

    <script src="https://sdk.mercadopago.com/js/v2"></script>
    <script src="https://sdk.mercadopago.com/js/v2"></script>

    <script src="/projeto_api/app/assets/js/script.js"></script>
</body>
</html>
