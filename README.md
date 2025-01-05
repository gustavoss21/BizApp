# <h1>O que é o BizApp? para que serve?<h1>
O projeto PHP com API que oferece acesso seguro a dados de clientes e produtos, 
além de autenticação via API REST dedicada.
Inclui uma área administrativa para gerenciar permissões de uso da API 
e um aplicativo integrado para consumo das funcionalidades.

## Funcionalidades

- **API de Clientes**: Gerenciamento de clientes (criar, listar, atualizar e excluir).
- **API de Produtos**: Gerenciamento de produtos (criar, listar, atualizar e excluir).
- **Autenticação**: Sistema de login e tokens para segurança.
- **Área Administrativa**: Permite o gerenciamento de usuários que podem acessar a API.
- **App Consumo da API**: Exemplo de aplicação que consome a API (ex.: utilizando cURL ou uma biblioteca HTTP).

## Requisitos:

- PHP >= 7.4
- Composer
- Banco de Dados (MySQL, Postgres ou outro compatível)
- Servidor Web (Apache, Nginx, etc.)

## Instalação

1. Clone o repositório
2. cd seu-repositorio
3. composer install

## configurações iniciais
1. abra api/inc/config.php
2. defina os parametros do banco de dados

## Rotas da API

### Autenticação
POST /api/auth/login: retorna o user e o token.
<h6>Payload:</h6> { "email": "usuario@example.com", "password": "senha" }

### Clientes
GET /api/clientes/?endpoint={<i>endpoint</i>} <b>Lista todos os clientes.</b>

GET /api/clientes/?endpoint={<i>endpoint</i>}&filter={<i>filter:value;filter:value</i>}: <b>Exibe os detalhes de um cliente.</b>

POST /api/clientes/?endpoint={<i>endpoint</i>}: <b>Cria, atualizar ou deletar cliente.</b>

<i>Payload: { "nome": "Cliente X", "email": "cliente@example.com", "telefone": "12345678" }</i>
<p><b>filters:</b></p>
<ul>
  <li>id</li>
  <li>nome</li>
  <li>email</li>
  <li>deleted_at</li>
  <li>active</li>
  <li>email</li>
  <li>inactive</li>
</ul>
<p><b>endpoints:</b></p>
<ul>
  <li>get_clients</li>
  <li>create_client</li>
  <li>update_clients</li>
  <li>destroy_clients</li>
</ul>

# Produtos
GET /api/produtos/?endpoint={<i>endpoint</i>}: <b>Lista todos os produtos.</b>

GET /api/produtos/?endpoint={<i>endpoint</i>}&filter={<i>filter:value;filter:value</i>}: <b>Lista produtos filtrados.</b>

POST /api/produtos/?endpoint={<i>endpoint</i>}:<b> Cria, atualizar ou deletar produto.</b>

Payload: { "nome": "Produto X", "preco": 100.00, "estoque": 50 }
<p><b>endpoints</b></p>
<ul>
  <li>get_products</li>
  <li>create_products</li>
  <li>update_products</li>
  <li>destroy_products</li>
</ul>

# Área Administrativa
GET /admin/user/?endpoint={<i>endpoint</i>}: <b>Lista os usuários que podem consumir a API.</b>

GET /admin/user/?endpoint={<i>endpoint</i>}&filter={<i>filter:value;filter:value</i>}}:<b>Lista usuários que podem consumir a API com filtro.</b>

POST /admin/user/?endpoint={<i>endpoint</i>}: <b>Cria, atualizar ou deletar usuário.</b>

Payload: { "nome": "Admin","tokken":"12345678iuhgfdewrtyhnbvfd", "password": "senha" }
<p><b>filters:</b></p>
<ul>
  <li>id</li>
  <li>nome</li>
  <li>deleted_at</li>
  <li>active</li>
  <li>inactive</li>
</ul>
<p><b>endpoints:</b></p>
<ul>
  <li>getUsers</li>
  <li>createUser</li>
  <li>updateUser</li>
  <li>activeUser</li>
  <li>destroyUser</li>
</ul>

# next updates
- prevenir injeções de código e sanitizar a variável diretamente da superglobal $_REQUEST.
- padronizar nomeclaturas
- updateUser - tirar o validation parameter e definilos pos intersection
- adicionar um parenteses em parametros exclusivos deve usar expressao regular
ex: 
select * from teste where (nome ='xxx' or idade='yyy') and id <> :id

- Autoloading e Namespaces
Utilize namespaces para evitar conflitos de nomes e facilitar a organização do código.
Aproveite o PSR-4 autoloading para carregar automaticamente suas classes, evitando require ou include manuais.

- Testes Automatizados
Escreva testes para suas funções (usando PHPUnit, por exemplo) para garantir que o código funcione conforme esperado.
