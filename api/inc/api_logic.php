<?php

class api_logic
{
    private $accepted_filters;
    public function __construct(private $params, private $endpoint)
    {
    }

    public function check_endpoint()
    {
        return method_exists($this, $this->endpoint);
    }

    public function setFilters($query_base, $filters)
    {
        $query_base .= ' where';
        $filters_to_query = [];
        $count = 0;
        foreach ($filters as $key => $filter) {

            $filters_to_query[':' .$key] = $filter;

            if(!$count > 0){
                @$query_base .= $this->accepted_filters[$key];
                continue;
            }

            $query_base .= ' and ' . $this->accepted_filters[$key];
            $count ++;

        }

        return [$query_base, $filters_to_query];
    }

    public function get_products($filters)
    {
        $this->accepted_filters = ['id_produto' => ' id_produto = :id_produto','deleted_at' => ' deleted_at is null'];
        $conection = new database();
        $query_base = 'select id_produto, produto, quantidade, created_at, updated_at from produtos';
        [$query,$filter_query] = is_null($filters) ? [$query_base,null] : $this->setFilters($query_base, $filters);
        $produto = $conection->EXE_QUERY($query, $filter_query);
        return $produto;
    }

    public function get_clients($filters)
    {
        $this->accepted_filters = ['id_cliente' => ' id_cliente = :id_cliente', 'deleted_at' => ' deleted_at is null'];
        $conection = new database();
        $query_base = 'select nome, email, telefone, created_at, updated_at from clientes';
        [$query,$filter_query] = is_null($filters) ? [$query_base,null] : $this->setFilters($query_base, $filters);
        $client = $conection->EXE_QUERY($query, $filter_query);
        return $client;
    }
}
