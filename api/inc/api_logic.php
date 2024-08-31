<?php

class api_logic
{
    private $accepted_filters;
    private $filters;
    public function __construct(private $params, private $endpoint)
    {
        $this->setFilter();
    }

    public function check_endpoint()
    {
        
        return method_exists($this, $this->endpoint);
    }

    public function setFilter(){
        $pathern = '/(([^:;]+):([^;\s]+))(;\1|$)/';

        if (!$this->params['filter'] || preg_match($pathern,$this->params['filter']) == 0) {
            return;
        }

        $filters = explode(';', $this->params['filter']);
        $pathern = '/.+?:+?/';

        if($this->params['filter']){
            foreach($filters as $filter){
                // $this->filters = explode(':', $filter);
                [$key,$value] = explode(':', $filter);
                $this->filters[$key] = $value; 
            }
        }
    }


    public function setQueryFilter($query_base)
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

    public function get_products()
    {
        $this->accepted_filters = ['quantidade'=>'quantidade = :quantidade','id_produto' => 'id_produto = :id_produto', 'deleted_at' => ' deleted_at is null'];
        $conection = new database();
        $query_base = 'select id_produto, produto, quantidade, created_at, updated_at from produtos';
        [$query,$filter_query] = $this->setQueryFilter($query_base);
        // return  ['error' => false, 'data' => [$query, $filter_query]];
        $produto = $conection->EXE_QUERY($query, $filter_query);
        return $produto;
    }

    public function get_clients($filters)
    {

        $this->accepted_filters = ['id_cliente' => ' id_cliente = :id_cliente', 'deleted_at' => ' deleted_at is null'];
        $conection = new database();
        $query_base = 'select id_cliente, nome, email, telefone from clientes';
        [$query,$filter_query] = $this->setQueryFilter($query_base);

        $cliente = $conection->EXE_QUERY($query, $filter_query);
        return $cliente;

    }
}
