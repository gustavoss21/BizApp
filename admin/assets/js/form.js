let inputFone = document.getElementById('fone_number');
let form = document.querySelector('#creatUseForm');

/**
 * formata o campo numero de telefone cada click
 */
inputFone.addEventListener('keydown',(event)=>{
    let special_digits = ['Enter','Backspace','Delete','ArrowLeft','ArrowRight'];
    let digite = event.key;
    let numberFone = event.target.value;
    let pattern = /^\(?(\d{2})\)?\s?(\d*)/
    numberFone += digite;
    
    //permite o uso dos botoes de apagar e direcionar 
    if(special_digits.includes(event.key)){
        return;
    }

    event.preventDefault()

    // evita inserção de tudo que nao for numero
    if(!Number.isInteger(Number(event.key))){
        return
    }

    //adicionar parentese no DDD
    numberFone = numberFone.replace(pattern,'($1) $2')
    event.target.value = numberFone;
})


/**
 * formata o campo numero de telefone cada mudança
 */
inputFone.addEventListener('change',(event)=>{
    let numberFone = event.target.value;
    let pattern = /(\+\d{2})?\s?\(?(\d{2})\)?\s?(\d{8,9})/

    //adicionar parentese no DDD
    numberFone = numberFone.replace(pattern,'$1 ($2) $3')
    event.target.value = numberFone;

})

/**
 * envia o formulario de criação de usuário
 */
form.addEventListener('submit',event=>{
    event.preventDefault();

    let form = event.target.cloneNode(true)
    let elemnt_message = document.querySelector('#message')
    let inputFone = form.querySelector('#fone_number');
    let inputddd = form.querySelector('input[name=fone_area_code]');
    
    elemnt_message.style.visibility = 'visible'

    //splint number
    let pattern = /(\+[0-9]{2})?\s?\(([0-9]{2})\)\s?(\d{8,9})/;
    let ddd = inputFone.value.replace(pattern,'$2');
    let foneNumber = inputFone.value.replace(pattern,'$3');

    //avoid repeated input 
    if(inputddd){
        inputFone.value = foneNumber;
        inputddd.setAttribute('value',ddd);
        return
    }

    //add input DDD OF FONE
    let inputDDD = document.createElement('input')
    inputDDD.setAttribute('name','fone_area_code');
    inputDDD.setAttribute('value',ddd);
    form.appendChild(inputDDD);
    
    // add new value the foneNumber
    inputFone.value = foneNumber;

    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries()); // Converte para objeto

    setToken()

    fetch(location.origin +'/projeto_api/admin/user/store.php',{
        method:'POST',
        headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(data) // Envia como JSON
    })
    .then(
        
        response => response.json()
    )
    .then(
        response=>{

            if(response.status === 'success'){
                let token = formData.get('tokken')
                location.href = '/projeto_api/admin/user/status.php?tokken='+token
                return
            }
            let message = document.querySelector('#message')

            message.innerHTML = response['message']['msg']
            message.style.color = response['message']['color']
            message.style.marginTop = '15px'
            message.style.textAlign = 'center'

            let arrayError = response.input_error

            Object.keys(arrayError).forEach((value,key)=>{
                let seletor = '#error-'+value
                console.log(seletor)
                let input = document.querySelector(seletor)
                if(input){
                    input.setAttribute('class','error-active')
                    input.innerHTML = arrayError[value];
                }
            })

            setTimeout(()=>{
                elemnt_message.style.visibility = 'hidden'
            },10000)
       }
        
    )
    .catch(
        error => {
            console.log('error');
            console.log(error)
            
            setTimeout(()=>{
                elemnt_message.style.visibility = 'hidden'
            },10000)
        }

    )

})


function generateToken(length = 32) {
    const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let token = '';
    
    for (let i = 0; i < length; i++) {
        const randomIndex = Math.floor(Math.random() * characters.length);
        token += characters[randomIndex];
    }
    
    return token;
}


function setToken(){
    let elementPassword = document.querySelector('#password');
let elementToken = document.querySelector('#tokken');
    elementPassword.value = generateToken();
    elementToken.value = generateToken();
}

setToken();