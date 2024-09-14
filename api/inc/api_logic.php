<?php

use React\Dns\Model\Message;
use React\Dns\Query\Query;

use function React\Promise\any;

class api_logic
{
    private $filters;
    private $method;

    public function __construct(private $params, private $endpoint)
    {
    }

    public function check_endpoint()
    {
        return method_exists($this, $this->endpoint);
    }

    protected function setFilter(): array
    {
        $pathern = '/(([^:;]+):([^;\s]+))(;\1|$)/';

        if (!isset($this->params['filter']) || preg_match($pathern, $this->params['filter']) == 0) {
            return [];
        }

        $filters = explode(';', $this->params['filter']);
        $pathern = '/.+?:+?/';
        $filters_formated = [];

        if ($this->params['filter']) {
            foreach ($filters as $filter) {
                [$key,$value] = explode(':', $filter);
                $filters_formated[$key] = $value;
            }
        }

        return $filters_formated;
    }

    protected function isssetParamasValidation($parameters)
    {
        $validation = ['valid' => true, 'erros' => [], 'data' => []];
        foreach ($parameters as $param) {
            if (!isset($this->params[$param])) {
                $validation['valid'] = false;
                $validation['erros'][] = 'o parametro ' . $param . ' é requisitado!';
                continue;
            }
            $validation['data'][$param] = trim($this->params[$param]);
        }
        return $validation;
    }

    protected function setQueryFilterSelect(array $query_tool, $filters, $accepted_filters)
    {
        if(count($filters) < 1){
            return [$query_tool['query'], $filters];
        }

        $query = '';
        $filters_to_query = [];
        $operatores = array_fill(0, count($filters) - 1, $query_tool['operator']);
        $count = 0;
        // return [$query, $filters];

        foreach ($filters as $key => $filter) {
            if(!isset($accepted_filters[$key])){continue;}
            $filters_to_query[':' . $key] = $filter;
            @$query .=  $accepted_filters[$key] . $operatores[$count];
            $count++;

        }

        if(!$filters_to_query){
            return [$query_tool['query'], []];
        }

        $query = $query_tool['query'] . ' where ' . $query;
        return [$query, $filters_to_query];

    }

    protected function setQueryParams($parameters)
    {
        $parameters_fomated = [];
        foreach ($parameters as $key => $filter) {
            $parameters_fomated[':' . $key] = $filter;
        }

        return $parameters_fomated;
    }

    // check in database if the parameters nome or email exist
    protected function exist(array $query_base, array $parameters, $accepted_filters)
    {
        [$query,$parameters] = $this->setQueryFilterSelect($query_base, $parameters, $accepted_filters);
        $conection = new database();
        $result = $conection->EXE_QUERY($query, $parameters);

        return $result;
    }

    protected function responseError($e)
    {
        $error = ['data' => '', 'message' => $e, 'error' => true];
        return $error;
    }

    protected function response($data, $message = '')
    {
        return ['data' => $data, 'message' => $message, 'error' => false];
    }

    protected function set_null_deleted_at($db,$identifaier){
        $query = "UPDATE $db SET deleted_at=null WHERE $identifaier";
        $connection = new database();
        $connection->EXE_NON_QUERY($query);
    }

    public function setMethod($method)
    {
        $this->method = $method;
    }

    public function get_products()
    {
        $filters = $this->setFilter();
        $deleted_at = isset($filters['deleted_at']) && $filters['deleted_at']? 'deleted_at is not null' : 'deleted_at is null';


        $accepted_filters = ['quantidade' => 'quantidade = :quantidade', 'id_produto' => 'id_produto = :id_produto','produto' => 'produto = :produto'];
        $conection = new database();
        $operator = ' and ';
        $query_tool = ['query' => 'select id_produto, produto, quantidade from produtos', 'operator' => $operator];

        [$query,$filter_query] = $this->setQueryFilterSelect($query_tool, $filters, $accepted_filters);

        $query = $filter_query? $query . $operator . $deleted_at : $query .' where '. $deleted_at;
        // return $this->responseError([$query, $filter_query]);

        $produto = $conection->EXE_QUERY($query, $filter_query);
        if ($produto) {
            $this->responseError('produto não encontrado');
        }
        return $this->response($produto, 'product ok');
    }

    public function get_products_all()
    {
        $filters = $this->setFilter();

        $accepted_filters = ['quantidade' => 'quantidade = :quantidade', 'id_produto' => 'id_produto = :id_produto'];
        $conection = new database();
        $query_tool = ['query' => 'select id_produto, produto, quantidade from produtos', 'operator' => ' and '];
        [$query,$filter_query] = $this->setQueryFilterSelect($query_tool, $filters, $accepted_filters);
        return $this->responseError([$query, $filter_query]);

        $produto = $conection->EXE_QUERY($query, $filter_query);
        if ($produto) {
            $this->responseError('produto não encontrado');
        }
        return $this->response($produto, 'product ok');
    }

    public function get_clients()
    {
        // by default get only those with null deleted_at
        $filters = $this->setFilter();
        $deleted_at = isset($filters['deleted_at']) && $filters['deleted_at']? 'deleted_at is not null' : 'deleted_at is null';


        $accepted_filters = ['id_cliente' => ' id_cliente = :id_cliente'];

        $conection = new database();
        $operator = ' and ';
        $query_base = ['query' => 'select id_cliente, nome, email, telefone from clientes', 'operator' => $operator];

        [$query,$filter_query] = $this->setQueryFilterSelect($query_base, $filters, $accepted_filters);
        
        $query = $filter_query? $query . $operator . $deleted_at : $query .' where '. $deleted_at;
        // return $this->responseError([$query, $filter_query]);

        $cliente = $conection->EXE_QUERY($query, $filter_query);
        if (!$cliente) {
            $this->responseError('Cliente não encontrado');
        }

        return $this->response($cliente, 'client ok');
    }



    public function create_clients()
    {
        if ($this->method != 'POST') {
            return $this->responseError('method is not permition');
        }

        //inputs required
        $params = ['nome', 'email', 'telefone'];

        //checks that the parameters are set
        $is_invalid = $this->isssetParamasValidation($params);
        if (!$is_invalid['valid']) {
            return $this->responseError($is_invalid['erros']);
        }

        //check if exist register of inputs
        $is_unique_inputs = ['email' => 'email = :email', 'nome' => 'nome = :nome'];
        $query = 'insert into clientes (nome, email, telefone) values(:nome, :email, :telefone)';

        $check_exists_database = array_intersect_key($is_invalid['data'], $is_unique_inputs);
        $verification_query = ['query' => "select id_cliente, email, deleted_at from clientes", 'operator' => ' or '];

        $is_exist = $this->exist($verification_query, $check_exists_database, $is_unique_inputs);

        if (count($is_exist) > 0) {
            if(($is_exist[0]['deleted_at'])){
                $this->set_null_deleted_at('clientes',"id_cliente = {$is_exist[0]['id_cliente']}");
                return $this->responseError(' Cliente existe inativo, Cliente foi ativado!');

            }
            return $this->responseError($is_exist);
            // return $this->responseError('email ou nome já está cadastrado');
        }

        $params_to_query = $this->setQueryParams($is_invalid['data']);

        $connection = new database();
        $result = $connection->EXE_NON_QUERY($query, $params_to_query);
        if (!$result) {
            return $this->responseError('hove um error inesperado');
        }

        return $this->response($result, 'inserction success');
    }

    public function update_client()
    {
        if ($this->method != 'POST') {
            return $this->responseError('method is not permition');
        }

        //inputs required
        $params = ['id_cliente', 'email', 'telefone'];

        //checks that the parameters are set
        $is_invalid = $this->isssetParamasValidation($params);
        if (!$is_invalid['valid']) {
            return $this->responseError($is_invalid['erros']);
        }

        $params_to_query = $this->setQueryParams($is_invalid['data']);
        $query = 'update clientes set email = :email, telefone = :telefone where id_cliente = :id_cliente';

        // return $this->responseError([$query, $params_to_query]);

        $connection = new database();
        $result = $connection->EXE_NON_QUERY($query, $params_to_query);
        if (!$result) {
            return $this->responseError('hove um error inesperado');
        }

        return $this->response($result, 'inserction success');
    }

    public function create_products()
    {
        if ($this->method != 'POST') {
            return $this->responseError('method is not permition');
        }

        //inputs required
        $params = ['produto', 'quantidade'];

        //checks that the parameters are set
        $is_invalid = $this->isssetParamasValidation($params);
        if (!$is_invalid['valid']) {
            return $this->responseError($is_invalid['erros']);
        }

        //check if exist register of inputs
        $is_unique_inputs = ['produto' => 'produto = :produto'];
        $check_exists_database = array_intersect_key($is_invalid['data'], $is_unique_inputs);
        $verification_query = ['query' => "select id_produto, deleted_at from produtos", 'operator' => ''];
        $is_exist = $this->exist($verification_query, $check_exists_database, $is_unique_inputs);

        if (count($is_exist) > 0) {
            if(($is_exist[0]['deleted_at'])){
                $this->set_null_deleted_at('produtos',"id_produto = {$is_exist[0]['id_produto']}");
                return $this->responseError(' produto existe inativo, produto foi ativado!');

            }
            return $this->responseError('O produto já está cadastrado');
        }

        // performs the insertion
        $params_to_query = $this->setQueryParams($is_invalid['data']);
        $query = 'insert into produtos (produto, quantidade) values(:produto, :quantidade)';

        $connection = new database();
        $result = $connection->EXE_NON_QUERY($query, $params_to_query);
        if (!$result) {
            return $this->responseError('hove um error inesperado');
        }

        return $this->response($result, 'inserction success');
    }

    public function destroy_client()
    {
        if ($this->method != 'POST') {
            return $this->responseError('method is not permition');
        }

        //inputs required
        $params = ['id_cliente'];

        //checks that the parameters are set
        $is_invalid = $this->isssetParamasValidation($params);
        if (!$is_invalid['valid']) {
            return $this->responseError($is_invalid['erros']);
        }

        //check if exist register of inputs
        $check_exist_inputs = ['id_cliente' => 'id_cliente = :id_cliente'];

        $check_exists_database = array_intersect_key($is_invalid['data'], $check_exist_inputs);
        $verification_query = ['query' => "select id_cliente, deleted_at from clientes", 'operator' => ' and '];

        $is_exist = $this->exist($verification_query, $check_exists_database, $check_exist_inputs);

        if (count($is_exist) <= 0) {
            return $this->responseError('cliente não encontrado, tente mais tarde!');
        }

        $params_to_query = $this->setQueryParams($is_invalid['data']);
        $query = 'UPDATE clientes SET deleted_at=now() WHERE id_cliente = :id_cliente';

        $connection = new database();
        $result = $connection->EXE_NON_QUERY($query, $params_to_query);
        if (!$result) {
            return $this->responseError('houve um erro inesperado');
        }

        return $this->response('', 'remove success');
    }

    public function destroy_product()
    {
        if ($this->method != 'POST') {
            return $this->responseError('method is not permition');
        }

        //inputs required
        $params = ['id_produto'];

        //checks that the parameters are set
        $is_invalid = $this->isssetParamasValidation($params);
        if (!$is_invalid['valid']) {
            return $this->responseError($is_invalid['erros']);
        }

        //check if exist register of inputs
        $check_exist_inputs = ['id_produto' => 'id_produto = :id_produto'];

        $check_exists_database = array_intersect_key($is_invalid['data'], $check_exist_inputs);
        $verification_query = ['query' => "select id_produto, deleted_at from produtos", 'operator' => ' and '];
        $is_exist = $this->exist($verification_query, $check_exists_database, $check_exist_inputs);

        if (count($is_exist) <= 0) {
            return $this->responseError('produto não encontrado, tente mais tarde!');
        }

        $params_to_query = $this->setQueryParams($is_invalid['data']);
        $query = 'UPDATE produtos SET deleted_at=now() WHERE id_produto = :id_produto';

        $connection = new database();
        $result = $connection->EXE_NON_QUERY($query, $params_to_query);
        if (!$result) {
            return $this->responseError('houve um erro inesperado');
        }

        return $this->response('', 'remove success');
    }
}
