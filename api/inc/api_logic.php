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

    protected function isValidParameter($parameter, $parameters)
    {
        $methods_validation = [];
        $methods_validation['min_4'] = fn ($nome) => preg_match('/.{4}/', $nome);
        $methods_validation['min'] = fn ($numero) => preg_match('/^\d{11}$/', $numero);
        $methods_validation['int'] = fn ($numero) => preg_match('/^\d+$/', $numero);
        $methods_validation['email'] = fn ($email) => filter_var($email, FILTER_VALIDATE_EMAIL);

        foreach ($parameters as  $param_value) {
            $value_for_validation = $parameter;
            if (!$methods_validation[$param_value]($value_for_validation)) {
                return false;
            };
        }
        return true;
    }

    protected function isssetParamasValidation($parameters)
    {
        $validation = ['valid' => true, 'erros' => [], 'data' => []];

        foreach ($parameters as $key => $validatores) {
            if (!isset($this->params[$key]) or !$this->params[$key]) {
                $validation['valid'] = false;
                $validation['erros'][$key] = 'o parametro ' . $key . ' é requisitado!';
                continue;
            }
            $parameter = trim($this->params[$key]);
            $is_valid = $this->isValidParameter($parameter, $validatores);
            if (!$is_valid) {
                $validation['valid'] = false;
                $validation['erros'][$key] = 'o parametro ' . $key . ' é invalido!';
                continue;
            }

            $validation['data'][$key] = $parameter;
        }

        return $validation;
    }

    protected function setQueryFilterSelect(string $query_base, $filters, $accepted_filters)
    {
        if (count($filters) < 1) {
            return [$query_base, $filters];
        }

        $query = '';
        $filters_to_query = [];
        $keypast = '';
        $are_not_filters_for_consultation = ['active', 'inactive'];

        foreach ($filters as $key => $filter) {
            if (!isset($accepted_filters[$key])) {
                continue;
            }

            if ($keypast) {
                $query .= $accepted_filters[$keypast]['operator'];
            }

            $query .= $accepted_filters[$key]['param'];
            $keypast = $key;

            if (in_array($key, $are_not_filters_for_consultation)) {
                continue;
            }

            $filters_to_query[':' . $key] = $filter;
        }

        if (!$query) {
            return [$query_base, []];
        }

        $query = $query_base . ' where ' . $query;
        return [$query, $filters_to_query];
    }

    protected function setQueryParams(array $parameters)
    {
        $parameters_fomated = [];
        $are_not_params_for_consultation = ['active', 'inactive'];
        foreach ($parameters as $key => $filter) {
            if (in_array($key, $are_not_params_for_consultation)) {
                continue;
            }
            $parameters_fomated[':' . $key] = $filter;
        }

        return $parameters_fomated;
    }

    // check in database if the parameters nome or email exist
    protected function exist(string $query_base, array $parameters, $accepted_filters)
    {
        [$query,$parameters] = $this->setQueryFilterSelect($query_base, $parameters, $accepted_filters);
        // return  [$query,$parameters];

        $conection = new database();
        $result = $conection->EXE_QUERY($query, $parameters);

        return $result;
    }

    protected function responseError($m, array $input_error = [])
    {
        $error = ['data' => '', 'message' => $m, 'input_error' => $input_error, 'error' => true];
        return $error;
    }

    protected function response($data, $message, array $input_error = [])
    {
        return ['data' => $data, 'message' => $message, 'input_error' => $input_error, 'error' => false];
    }

    protected function set_null_deleted_at($db, $identifaier)
    {
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

        $accepted_filters = [
            'quantidade' => ['param' => 'quantidade = :quantidade', 'operator' => ''],
            'id_produto' => ['param' => 'id_produto = :id_produto', 'operator' => ' end '],
            'produto' => ['param' => 'produto = :produto', 'operator' => ' end '],
            'deleted_at' => ['param' => 'deleted_at = :deleted_at', 'operator' => ' and '],
            'active' => ['param' => 'deleted_at is null', 'operator' => ' and '],
            'inactive' => ['param' => 'deleted_at is not null', 'operator' => ' and '],
        ];

        $query_base = 'select id_produto, produto, quantidade, deleted_at from produtos';

        [$query,$filter_query] = $this->setQueryFilterSelect($query_base, $filters, $accepted_filters);

        $conection = new database();
        $produto = $conection->EXE_QUERY($query, $filter_query);

        if (!$produto) {
            $this->responseError('produto não encontrado');
        }

        return $this->response($produto, 'product ok');
    }

    public function get_products_all()
    {
        $filters = $this->setFilter();

        $accepted_filters = [
            'quantidade' => ['param' => 'quantidade = :quantidade', 'operator' => ''],
        ];
        $conection = new database();
        $query_base = 'select id_produto, produto, quantidade from produtos';
        [$query,$filter_query] = $this->setQueryFilterSelect($query_base, $filters, $accepted_filters);

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

        $accepted_filters = [
            'id_cliente' => ['param' => 'id_cliente = :id_cliente', 'operator' => ' and '],
            'nome' => ['param' => 'nome = :nome', 'operator' => ' and '],
            'email' => ['param' => 'email = :email', 'operator' => ' and '],
            'deleted_at' => ['param' => 'deleted_at = :deleted_at', 'operator' => ' and '],
            'active' => ['param' => 'deleted_at is null', 'operator' => ' and '],
            'inactive' => ['param' => 'deleted_at is not null', 'operator' => ' and '],
        ];

        $query_base = 'select id_cliente, nome, email, telefone from clientes';
        // return $this->response([$filters, $accepted_filters], 'client ok');

        [$query,$filter_query] = $this->setQueryFilterSelect($query_base, $filters, $accepted_filters);

        $conection = new database();
        $cliente = $conection->EXE_QUERY($query, $filter_query);
        if (!$cliente) {
            $this->responseError('Cliente não encontrado');
        }

        return $this->response($cliente, 'client ok');
    }

    public function update_product()
    {
        if ($this->method != 'POST') {
            return $this->responseError('method is not permition');
        }

        //inputs required
        $params = ['id_produto' => ['int'], 'produto' => ['min_4'], 'quantidade' => ['int']];

        //checks that the parameters are set
        $is_invalid = $this->isssetParamasValidation($params);
        if (!$is_invalid['valid']) {
            return $this->responseError('existem parâmetros inválidos', $is_invalid['erros']);
        }

        //check if exist product registed
        $is_unique_inputs = [
            'produto' => ['param' => 'produto = :produto', 'operator' => ' and '],
            'id_produto' => ['param' => 'id_produto <> :id_produto', 'operator' => ' and ']];
        $check_exists_database = array_intersect_key($is_invalid['data'], $is_unique_inputs);
        $query_base = 'select id_produto, deleted_at from produtos';
        $is_exist = $this->exist($query_base, $check_exists_database, $is_unique_inputs);

        if (count($is_exist) > 0) {
            if (($is_exist[0]['deleted_at'])) {
                $this->set_null_deleted_at('produtos', "id_produto = {$is_exist[0]['id_produto']}");
                return $this->responseError(' produto existe inativ, produto foi ativado!');
            }

            return $this->responseError('O produto já está cadastrado');
        }

        // performs the insertion
        $params_to_query = $this->setQueryParams($is_invalid['data']);
        $query = 'update produtos set produto = :produto, quantidade = :quantidade where id_produto = :id_produto';

        $connection = new database();
        $result = $connection->EXE_NON_QUERY($query, $params_to_query);
        if (!$result) {
            return $this->responseError('hove um error inesperado');
        }

        return $this->response($result, 'inserction success');
    }

    public function create_clients()
    {
        if ($this->method != 'POST') {
            return $this->responseError('method is not permition');
        }

        //inputs required
        $params = ['nome' => ['min_4'], 'email' => ['email'], 'telefone' => ['min']];

        //checks that the parameters are set
        $is_invalid = $this->isssetParamasValidation($params);

        if (!$is_invalid['valid']) {
            return $this->responseError('existem parâmetros inválidos', $is_invalid['erros']);
        }

        //check if exist register of inputs
        $is_unique_inputs = ['email' => ['param' => 'email = :email', 'operator' => ''], 'nome' => ['param' => 'nome = :nome', 'operator' => ' or ']];
        $query = 'insert into clientes (nome, email, telefone) values(:nome, :email, :telefone)';

        $check_exists_database = array_intersect_key($is_invalid['data'], $is_unique_inputs);
        $query_base = 'select id_cliente, email, deleted_at from clientes';

        $is_exist = $this->exist($query_base, $check_exists_database, $is_unique_inputs);

        if (count($is_exist) > 0) {
            if (($is_exist[0]['deleted_at'])) {
                $this->set_null_deleted_at('clientes', "id_cliente = {$is_exist[0]['id_cliente']}");
                return $this->responseError(' Cliente existe inativo, Cliente foi ativado!');
            }
            return $this->responseError('email ou nome já está cadastrado');
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
        $params = ['id_cliente' => ['int'], 'email' => ['email'], 'telefone' => ['min']];

        //checks that the parameters are set
        $is_invalid = $this->isssetParamasValidation($params);

        if (!$is_invalid['valid']) {
            return $this->responseError('existem parâmetros inválidos', $is_invalid['erros']);
        }

        //check if exist client registed
        $is_unique_inputs = [
            'email' => ['param' => 'email = :email', 'operator' => ' or '],
            'id_cliente' => ['param' => 'id_cliente <> :id_cliente', 'operator' => ' and ']
        ];
        $check_exists_database = array_intersect_key($is_invalid['data'], $is_unique_inputs);
        $query_base = 'select id_cliente, email, deleted_at from clientes';

        $is_exist = $this->exist($query_base, $check_exists_database, $is_unique_inputs);
        // return $this->responseError($is_exist);

        if (count($is_exist) > 0) {
            if (($is_exist[0]['deleted_at'])) {
                $this->set_null_deleted_at('clientes', "id_cliente = {$is_exist[0]['id_cliente']}");
                return $this->responseError(' Cliente existe inativo, Cliente foi ativado!');
            }
            return $this->responseError('email ou nome já está cadastrado');
        }

        $params_to_query = $this->setQueryParams($is_invalid['data']);
        $query = 'update clientes set email = :email, telefone = :telefone where id_cliente = :id_cliente';

        $connection = new database();
        $result = $connection->EXE_NON_QUERY($query, $params_to_query);
        if (!$result) {
            return $this->responseError('hove um error inesperado');
        }

        return $this->response($result, 'update success');
    }

    public function create_products()
    {
        if ($this->method != 'POST') {
            return $this->responseError('method is not permition');
        }

        //inputs required
        $params = ['produto' => ['min_4'], 'quantidade' => ['int']];

        //checks that the parameters are set
        $is_invalid = $this->isssetParamasValidation($params);
        if (!$is_invalid['valid']) {
            return $this->responseError('existem parâmetros inválidos', $is_invalid['erros']);
        }

        //check if exist register of inputs
        $is_unique_inputs = ['produto' => ['param' => 'produto = :produto', 'operator' => '']];
        $check_exists_database = array_intersect_key($is_invalid['data'], $is_unique_inputs);
        $query_base = 'select id_produto, deleted_at from produtos';
        $is_exist = $this->exist($query_base, $check_exists_database, $is_unique_inputs);

        if (count($is_exist) > 0) {
            if (($is_exist[0]['deleted_at'])) {
                $this->set_null_deleted_at('produtos', "id_produto = {$is_exist[0]['id_produto']}");
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
        $params = ['id_cliente' => ['int']];
        //checks that the parameters are set
        $is_invalid = $this->isssetParamasValidation($params);
        $is_invalid['data']['active'] = true;
        if (!$is_invalid['valid']) {
            return $this->responseError('existem parâmetros inválidos', $is_invalid['erros']);
        }

        //check if exist register of inputs
        $check_exist_inputs = [
            'id_cliente' => ['param' => 'id_cliente = :id_cliente', 'operator' => ' and '],
            'active' => ['param' => 'deleted_at is null', 'operator' => ' and '],
        ];

        $check_exists_database = array_intersect_key($is_invalid['data'], $check_exist_inputs);
        $query_base = 'select id_cliente, deleted_at from clientes';

        $is_exist = $this->exist($query_base, $check_exists_database, $check_exist_inputs);

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

        return $this->response($result, 'remove success');
    }

    public function destroy_product()
    {
        if ($this->method != 'POST') {
            return $this->responseError(['method is not permition']);
        }

        //inputs required
        $params = ['id_produto' => ['int']];

        //checks that the parameters are set
        $is_invalid = $this->isssetParamasValidation($params);
        $is_invalid['data']['active'] = true;

        if (!$is_invalid['valid']) {
            return $this->responseError('existem parâmetros inválidos', $is_invalid['erros']);
        }

        //check if exist register of inputs
        $check_exist_inputs = [
            'id_produto' => ['param' => 'id_produto = :id_produto', 'operator' => ' and '],
            'active' => ['param' => 'deleted_at is null', 'operator' => ' and ']
        ];

        $check_exists_database = array_intersect_key($is_invalid['data'], $check_exist_inputs);
        $query_base = 'select id_produto, deleted_at from produtos';
        $is_exist = $this->exist($query_base, $check_exists_database, $check_exist_inputs);

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
