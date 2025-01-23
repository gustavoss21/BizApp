console.log(location.search);
let resend_email = document.getElementById('resend-email');
let user_token = document.getElementById('user-token');

resend_email.addEventListener('click',(event)=>{
    event.preventDefault();
    let origin = location.origin
    let href = origin+'/projeto_api/api/?endpoint=resend-validation-email&token='+user_token.value;
    console.log(href)
    fetch(href,{
        method:'GET',
      
    })
    .then(
        response => response.json()
    )
    .then(
        response=>{
            console.log('tudo certo')
            console.log(response)
       }
        
    )
    .catch(
        error => {
            console.log('error');
            console.error(error);
        
        }

    )
})

let button_pay = document.getElementById('pay');
button_pay.addEventListener('click',()=>{
    alert('para fazer o pagamento, validar o email primerio é requisitado!')
})