<html>
    <head>
      <script src="https://sdk.mercadopago.com/js/v2"></script>
    </head>
    <body>
      <div id="statusScreenBrick_container"></div>
      <script>
        let arrToobj = string=>{
            let array = location.search.split(/[&=]/)
            array[0] = array[0].replace('?','')
            let obj = {};

            for(let x = 0; x < array.length;){
                obj[array[x]] = array[x+1];x += 2;
            }
            
            return obj;
        }

        function copyToClickBoard(qr_code){
          navigator.clipboard.writeText(qr_code)
              .then(() => {
              console.log("Text copied to clipboard...")
          })
              .catch(err => {
              console.log('Something went wrong', err);
          })
          
        }

        function paymentPix(){
          let {id,id_payment,user} = arrToobj(location.search)
          let url = location.origin +'/projeto_api/api/index.php?endpoint=get_payment_From_api&id='+id_payment
          fetch(url,{
            method:'GET'
          })
          .then(
              
              response => response.json()
          )
          .then(
              response=>{
                console . log(response);
                let body = `
                  <h3 style="    
                    padding: 18px;
                    background-color: #ffe902;
                    border-radius: 10px 10px 0 0;
                    color: #5c5c5c;
                    font-size: 1.5rem;
                    text-align: center;">
                      Falta pouco!
                  </h3>
                  <div style="width: 60%; margin: auto; text-align: center;">
                    <div style="font-weight: bold; font-size: 1.2rem;">conclua o pagamento de ${response['data']['transaction_amount']}R$ via pix</div>
                    <div>Código válido até ${response['data']['expire_in']}</div>
                    <div style="display: flex; flex-direction: column; align-items: center; gap: 35px;padding: 10px; border: 1px solid #00000036">
                      <span>
                        Para pagar, copie e cole o código de pagamento na opção “Pagamento via Pix” no app,
                        ou abra o código QR para escaneá-lo.
                      </span>
                      <img width="200px" src="data:image/png;base64, ${response['data']['qr_code_base64']}" />
                      <div style="width:100%">
                        <input style="font-size: 1.5rem; width: 100%;" type="text" id="copiar" value=${response['data']['qr_code']} readonly/>
                        <button style="width: 100%; font-size: 1rem; margin-top: 10px; padding: 10px 0; border-radius: 5px; background: #95d4ff;" onclick="copyToClickBoard('${response['data']['qr_code']}')">Copiar Código</button>
                      </div>
                      <div style="display:flex;gap:10px">
                        <a href="http://127.0.0.1/projeto_api/admin/user/status.php?nome=${user}">Sobre o produto</a>
                        <a href="http://127.0.0.1/projeto_api/admin/checkout/index.php?id_payment=${id_payment}">outras formas de pagamento</a>
                      </div>
                  <div>`

                document . getElementsByTagName('body')[0] . innerHTML = body;
                // if(response.status === 'success'){
                //     let token = formData.get('tokken')
                //     location.href = '/projeto_api/admin/user/status.php?tokken='+token
                //     return
                // }
            }
              
          )
          .catch(
              error => {
                  console.log('error');
                  console.log(error)
                  
               
              }

          )
        }

        paymentPix()
      </script>
    </body>
    </html>