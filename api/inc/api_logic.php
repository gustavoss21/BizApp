<?php

use React\Dns\Model\Message;
use React\Dns\Query\Query;

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

    protected function setFilter():array
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
        $validation = ['valid' => false, 'erros' => [], 'data' => []];
        foreach ($parameters as $param) {
            if (!isset($this->params[$param])) {
                $validation['validate_error'] = true;
                $validation['erros'][] = 'o parametro ' . $param . ' é requisitado!';
                continue;
            }
            $validation['data'][$param] = $this->params[$param];
        }
        return $validation;
    }

    protected function setQueryFilterSelect(array $query_tool, $filters, $accepted_filters)
    {
        if (!$filters) {
            return [$query_tool['query'], null];
        }
        $query = $query_tool['query'];
        $filters_to_query = [];
        $count = 0;

        foreach ($filters as $key => $filter) {
            $filters_to_query[':' . $key] = $filter;
            $count++;

            $query .= " {$query_tool['operator']} " . $accepted_filters[$key];
        }

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
    protected function exist(array $params_db, array $parameters, $accepted_filters)
    {
        $query_base = ['query' => "select {$params_db['id']}  from {$params_db['db']} where deleted_at is null", 'operator' => $params_db['operator']];
        [$query,$parameters] = $this->setQueryFilterSelect($query_base, $parameters, $accepted_filters);
        $conection = new database();
        $result = $conection->EXE_QUERY($query, $parameters);

        return $result;
    }

    protected function responseError($e)
    {
        $error = ['data'=>'', 'message' => $e,'error' => true];
        return $error;
    }

    protected function response($data, $message = '')
    {
        return ['data'=>$data,'message'=>$message,'error'=>false];
    }

    public function setMethod($method)
    {
        $this->method = $method;
    }

    public function get_products()
    {
        $filters = $this->setFilter();

        $accepted_filters = ['quantidade' => 'quantidade = :quantidade', 'id_produto' => 'id_produto = :id_produto', 'deleted_at' => ' deleted_at is null'];
        $conection = new database();
        $query_tool = ['query' => 'select id_produto, produto, quantidade from produtos where deleted_at is null', 'operator' => 'and'];
        [$query,$filter_query] = $this->setQueryFilterSelect($query_tool, $filters, $accepted_filters);
        // return $this->responseError([$query, $filter_query]);

        $produto = $conection->EXE_QUERY($query, $filter_query);
        if($produto){
            $this->responseError('produto não encontrado');
        }
        return $this->response($produto, 'product ok');
    }

    public function get_clients()
    {
        // by default get only those with null deleted_at
        $filters = $this->setFilter();

        $accepted_filters = ['id_cliente' => ' id_cliente = :id_cliente', 'deleted_at' => ' deleted_at is not null'];

        $conection = new database();
        $query_base = ['query' => 'select id_cliente, nome, email, telefone from clientes where deleted_at is null', 'operator' => 'and'];

        [$query,$filter_query] = $this->setQueryFilterSelect($query_base, $filters, $accepted_filters);
        
        $cliente = $conection->EXE_QUERY($query, $filter_query);
        if(!$cliente){
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
        if ($is_invalid['valid']) {
            return $this->responseError($is_invalid['erros']);
        }

        //check if exist register of inputs
        $is_unique_inputs = ['email' => ' email = :email', 'nome' => '  nome = :nome'];
        $query = 'insert into clientes (nome, email, telefone) values(:nome, :email, :telefone)';

        $check_exists_database = array_intersect_key($is_invalid['data'], $is_unique_inputs);
        $params_db = ['id' => 'id_cliente', 'db' => 'clientes', 'operator' => 'and'];

        $is_exist = $this->exist($params_db, $check_exists_database, $is_unique_inputs);

        if (count($is_exist) > 0) {
            return $this->responseError('email ou nome já está cadastrado');
        }

        $params_to_query = $this->setQueryParams($is_invalid['data']);

        $connection = new database();
        $result = $connection->EXE_NON_QUERY($query, $params_to_query);
        if (!$result) {
            return $this->responseError('hove um error inesperado');
        }

        return $this->response($result,'inserction success');
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
        if ($is_invalid['valid']) {
            return $this->responseError($is_invalid['erros']);
        }

        //check if exist register of inputs
        $is_unique_inputs = ['produto' => ' produto = :produto'];
        $query = 'insert into produtos (produto, quantidade) values(:produto, :quantidade)';

        $check_exists_database = array_intersect_key($is_invalid['data'], $is_unique_inputs);
        $params_db = ['id' => 'id_produto', 'db' => 'produtos', 'operator' => 'and'];

        $is_exist = $this->exist($params_db, $check_exists_database, $is_unique_inputs);

        if (count($is_exist) > 0) {
            return $this->responseError('O produto já está cadastrado');
        }

        $params_to_query = $this->setQueryParams($is_invalid['data']);

        $connection = new database();
        $result = $connection->EXE_NON_QUERY($query, $params_to_query);
        if (!$result) {
            return $this->responseError('hove um error inesperado');
        }

        return $this->response($result,'inserction success');
    }

    public function destroy_client(){
        
        if ($this->method != 'POST') {
            return $this->responseError('method is not permition');
        }

        //inputs required
        $params = ['id_cliente'];

        //checks that the parameters are set
        $is_invalid = $this->isssetParamasValidation($params);
        if ($is_invalid['valid']) {
            return $this->responseError($is_invalid['erros']);
        }

        //check if exist register of inputs
        $check_exist_inputs = ['id_cliente'=>'id_cliente = :id_cliente'];

        $check_exists_database = array_intersect_key($is_invalid['data'], $check_exist_inputs);
        $params_db = ['id' => 'id_cliente', 'db' => 'clientes', 'operator' => 'and'];

        $is_exist = $this->exist($params_db, $check_exists_database, $check_exist_inputs);

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

        return $this->response('','remove success');
    }

    public function destroy_product(){
        
        if ($this->method != 'POST') {
            return $this->responseError('method is not permition');
        }

        //inputs required
        $params = ['id_produto'];

        //checks that the parameters are set
        $is_invalid = $this->isssetParamasValidation($params);
        if ($is_invalid['valid']) {
            return $this->responseError($is_invalid['erros']);
        }

        //check if exist register of inputs
        $check_exist_inputs = ['id_produto'=>'id_produto = :id_produto'];

        $check_exists_database = array_intersect_key($is_invalid['data'], $check_exist_inputs);
        $params_db = ['id' => 'id_produto', 'db' => 'produtos', 'operator' => 'and'];

        $is_exist = $this->exist($params_db, $check_exists_database, $check_exist_inputs);

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

        return $this->response('','remove success');
    }
}
