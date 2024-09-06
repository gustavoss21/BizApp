<?php

class api_logic
{
    private $accepted_filters;
    private $filters;
    private $method;

    public function __construct(private $params, private $endpoint)
    {
    }

    public function check_endpoint()
    {
        return method_exists($this, $this->endpoint);
    }

    protected function setFilter()
    {
        $pathern = '/(([^:;]+):([^;\s]+))(;\1|$)/';

        if (!isset($this->params['filter']) || preg_match($pathern, $this->params['filter']) == 0) {
            return;
        }

        $filters = explode(';', $this->params['filter']);
        $pathern = '/.+?:+?/';

        if ($this->params['filter']) {
            foreach ($filters as $filter) {
                // $this->filters = explode(':', $filter);
                [$key,$value] = explode(':', $filter);
                $this->filters[$key] = $value;
            }
        }
    }

    protected function setQueryFilterSelect($query_base)
    {
        if (!$this->filters) {
            return [$query_base, null];
        }

        $query_base .= ' where ';
        $filters_to_query = [];
        $count = 0;
        foreach ($this->filters as $key => $filter) {
            $filters_to_query[':' . $key] = $filter;
            $count++;
            if ($count <= 1) {
                @$query_base .= $this->accepted_filters[$key];
                continue;
            }

            $query_base .= ' and ' . $this->accepted_filters[$key];
        }

        return [$query_base, $filters_to_query];
    }

    protected function setQueryParams($parameters)
    {

        $parameters_fomated = [];
        foreach ($parameters as $key => $filter) {
            $parameters_fomated[':' . $key] = $filter;
        }

        return [$parameters_fomated];
    }

    public function setMethod($method){
        $this->method = $method;
    }

    public function get_products()
    {
        $this->setFilter();

        $this->accepted_filters = ['quantidade' => 'quantidade = :quantidade', 'id_produto' => 'id_produto = :id_produto', 'deleted_at' => ' deleted_at is null'];
        $conection = new database();
        $query_base = 'select id_produto, produto, quantidade from produtos';
        [$query,$filter_query] = $this->setQueryFilterSelect($query_base);
        // return  ['error' => false, 'data' => [$query, $filter_query]];
        $produto = $conection->EXE_QUERY($query, $filter_query);
        return $produto;
    }

    public function get_clients()
    {
        $this->setFilter();

        $this->accepted_filters = ['id_cliente' => ' id_cliente = :id_cliente', 'deleted_at' => ' deleted_at is null'];
        $conection = new database();
        $query_base = 'select id_cliente, nome, email, telefone from clientes';
        [$query,$filter_query] = $this->setQueryFilterSelect($query_base);

        $cliente = $conection->EXE_QUERY($query, $filter_query);
        return $cliente;
    }

    public function create_clients()
    {
        // return ['error' => false, 'data' => $this->params];

        if ($this->method != 'POST') {
            return ['error' => true, 'data' => 'method is not permition'];
        }
        $client = [
            'nome'=>$this->params['nome'],
            'email'=>$this->params['email'],
            'telefone'=>$this->params['telefone'],
        ];

        $query = 'insert into clientes (nome, email, telefone) values(:nome, :email, :telefone)';
        $params_to_query = $this->setQueryParams($client);

        $connection = new database();
        $result = $connection->EXE_NON_QUERY($query,$client);
        if(!$result){
            return ['error' => true, 'data' => 'hove um error inesperado'];
        }

        return ['error' => false, 'data' => 'inserction success'];
    }
}
