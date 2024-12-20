const mp = new MercadoPago("TEST-1216d8bc-8295-4cd8-85fc-31e086398a99");


// payload
let preference = {
  items: [
    {
      title: 'My product',
      unit_price: 100,
      quantity: 1
    }
  ]
};

mercadopago.preferences.create(preference).then(function(response){
  console.log(response);
}).catch(function(error){
  console.log(error);
});


const bricksBuilder = mp.bricks();
// custom email: test_user_1710964580@testuser.com
// public key: APP_USR-3f2af4db-50a8-4e53-8e88-1ceb83faf56e
// secret key: APP_USR-3370478573474690-110210-54db0537c7ff2f27682b65e913bff610-2071241495

const renderPaymentBrick = async (bricksBuilder) => {
    const settings = {
      initialization: {
        /*
         "amount" é o valor total a ser pago por todos os meios de pagamento
       com exceção da Conta Mercado Pago e Parcelamento sem cartão de crédito, que tem seu valor de processamento determinado no backend através do "preferenceId"
        */
        amount: 100,
        // preferenceId: 'preferenceId',
      },
      customization: {
        paymentMethods: {
          ticket: "all",
          bankTransfer: "all",
          creditCard: "all",
          debitCard: "all",
          mercadoPago: "all",
        },
      },
      callbacks: {
        onReady: () => {
          /*
           Callback chamado quando o Brick estiver pronto.
           Aqui você pode ocultar loadings do seu site, por exemplo.
          */
        },
        onSubmit: ({ selectedPaymentMethod, formData }) => {
            // location.pathname = '/projeto_api/app/checkout'
            // console.log([selectedPaymentMethod,formData]);
          // callback chamado ao clicar no botão de submissão dos dados
          return new Promise((resolve, reject) => {
            fetch("../checkout/process.php", {
              method: "POST",
              headers: {
                "Content-Type": "application/json",
              },
              body: JSON.stringify(formData),
            })
              .then((response) => response.json())
              .then((response) => {
                // receber o resultado do pagamento
                console.log(response);
                resolve();
              })
              .catch((error) => {
                // lidar com a resposta de erro ao tentar criar o pagamento
                reject();
              });
          });
        },
        onError: (error) => {
          // callback chamado para todos os casos de erro do Brick
          console.error(error);
        },
      },
    };
    window.paymentBrickController = await bricksBuilder.create(
      "payment",
      "paymentBrick_container",
      settings
    );
   };
   renderPaymentBrick(bricksBuilder);
   