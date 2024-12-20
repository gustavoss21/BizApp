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

        const mp = new MercadoPago('TEST-1216d8bc-8295-4cd8-85fc-31e086398a99', { // Add your public key credential
          locale: 'pt'
        });
        const bricksBuilder = mp.bricks();
        let {id,id_payment,user} = arrToobj(location.search)
        let data = {
            initialization: {
              paymentId: id, // Payment identifier, from which the status will be checked
            },
            customization: {
              visual: {
                hideStatusDetails: true,
                hideTransactionDate: true,
                style: {
                  theme: 'default', // 'default' | 'dark' | 'bootstrap' | 'flat'
                }
              },
              backUrls: {
                'error': 'http://127.0.0.1/projeto_api/admin/checkout/index.php?id_payment='+id_payment,
                'return': 'http://127.0.0.1/projeto_api/admin/user/status.php?nome='+user
              }
            },
            callbacks: {
              onReady: () => {
                // Callback called when Brick is ready
                console.log('tudo certo')

              },
              onError: (error) => {
                console.error(error)
                // Callback called for all Brick error cases
              },
            },
          };
        const renderStatusScreenBrick = async (bricksBuilder) => {
          const settings = data
          window.statusScreenBrickController = await bricksBuilder.create('statusScreen', 'statusScreenBrick_container', settings);
        };
        renderStatusScreenBrick(bricksBuilder);
      </script>
    </body>
    </html>