const mp = new MercadoPago("TEST-1216d8bc-8295-4cd8-85fc-31e086398a99");

let preferenceId = '';
let userAmount = '';
let user = {};

let data = (location.search).replace('?','')
data = data.replace('=',':')
const options = {
  method: 'GET',
  headers: {
    Accept: 'application/json',
  },
};

fetch('http://127.0.0.1/projeto_api/api/?endpoint=createPreference&filter='+data, options)
  .then(response => response.json())
  .then(response => {
    
    let data = response.data
    let user_data = data.user
    preferenceId = data.preference_id
    userAmount = data.amount
    user['nome'] = user_data.nome.split(' ')[0]
    user['user_full_name'] = user_data.nome
    user['email'] = user_data.email
    user['identification_type'] = user_data.identification_type
    user['identification_number'] = user_data.identification_number
    user['last_name'] = user_data.nome.split(/\s(?!.*\s)/)[1] ?? ''

    if(data.payment_Status === 'approved'){
      let id_external = data.id_external_payment
      let id_payment = location.search
      location.href = '/projeto_api/admin/checkout/status.php'+id_payment+'&id='+id_external_payment+'&user='+user['user_full_name']
    
    }

    renderPaymentBrick(bricksBuilder);
  })
  .catch(err => alert('houve um erro inesperado!'));



const bricksBuilder = mp.bricks();
// custom email: test_user_1710964580@testuser.com
// public key: APP_USR-3f2af4db-50a8-4e53-8e88-1ceb83faf56e
// secret key: APP_USR-3370478573474690-110210-54db0537c7ff2f27682b65e913bff610-2071241495

const renderPaymentBrick = async (bricksBuilder) => {
    let user_payer = {
      firstName: user.nome,
      lastName: user.last_name,
      email: user.email,
      identification: {
        type: user.identification_type,
        number: user.identification_number
      },
    }

    const settings = {
      initialization: {
        /*
         "amount" é o valor total a ser pago por todos os meios de pagamento
       com exceção da Conta Mercado Pago e Parcelamento sem cartão de crédito, que tem seu valor de processamento determinado no backend através do "preferenceId"
        */
        amount: userAmount,
        preferenceId: preferenceId,
        payer: user_payer
      },
      customization: {
        visual: {
          style: {
            theme: "default",
          },
        },
        paymentMethods: {
          creditCard: "all",
                  debitCard: "all",
                  ticket: "all",
                  bankTransfer: "all",
                  atm: "all",
          maxInstallments: 3
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
   
          // callback chamado ao clicar no botão de submissão dos dados
          sendPayment(formData);
            
           // receber o resultado do pagamento
           resolve();
        },
        onError: (error) => {
          // callback chamado para todos os casos de erro do Brick
          console.error(error);

           // manejar a resposta de erro ao tentar criar um pagamento
           reject();
        },
      },
    };

    window.paymentBrickController = await bricksBuilder.create(
      "payment",
      "paymentBrick_container",
      settings
    );
   };

  
function sendPayment(formData){
  // let data = Object.keys(formData)
  // .map(k => `${encodeURIComponent(k)}=${encodeURIComponent(formData[k])}`)
  // .join('&')
  let data = JSON.stringify(formData)

  fetch("../checkout/process.php", {
    method: "POST",
    body: data,
    headers: {
      'Content-Type': 'application/json',
    },
  })
    .then((response) => response.json())
    .then((response) => {
      // receber o resultado do pagamento
      if(response.status === 'SUCCESS'){
        //desmonta a brick
        window.paymentBrickController.unmount()

        let id_external = response.data.id_external
        let id_payment = location.search
        location.href = '/projeto_api/admin/checkout/status.php'+id_payment+'&id='+id_external+'&user='+user['user_full_name']
      }
      console.log(response);
      return response.data
    })
    .catch((error) => {
      // lidar com a resposta de erro ao tentar criar o pagamento
      console.error(error);
    });
}