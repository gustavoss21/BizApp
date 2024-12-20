<?php

namespace Api\action_route;

if(!isset($allowedRoute)){
    die('<div style="color:red;">Rota não encontrada</div>');
}

use Api\inc\Filter;
use Api\inc\Response;
use Api\inc\Validation;
use Api\inc\database;

class Product
{
    use Response;
    use Filter;
    use Validation;

    public function __construct(
        private int $id = 0, 
        private string $produto = '', 
        private string $quantidade = '',
        private string $deleted_at = '',
        private bool|null $active = false,
        private bool|null $inactive = false
        )
    {
    }

    /**
     * @return array <p>array with product attributes and their value
     */
    public function get_product_parameters()
    {
        return get_object_vars($this);
    }

    /**
     * @param string $parameter <p>product parameter</p>
     * @return string <p>product parameter value</p>
     */
    public function getParameter($parameter)
    {
        if (!isset($this->$parameter)) {
            return;
        }
        return $this->$parameter;
    }

    /**define product parameter
     * @param string $parameter <p>product parameter</p>
     * @param string $value <p>product parameter value</p>
     */
    public function setParameter($parameter, $value)
    {
        if (!isset($this->{$parameter})) {
            return;
        }

        $this->$parameter = trim($value);
    }

    /**
     *check if there is already a registered customer
     *@return array <p>returned response error or success
     */
    public function checkProductExists()
    {
        $isUniqueInputs = [
            'produto' => ['param' => 'produto = :produto', 'operator' => ' or ', 'exclusive' => true],
            'id' => ['param' => 'id_produto <> :id', 'operator' => ' and ', 'exclusive' => false]
        ];
        $productParametersToValidation = $this->get_product_parameters();
        $parametersToAvoidDuplication = array_intersect_key($productParametersToValidation, $isUniqueInputs);
        $queryBase = 'select id_produto, produto, quantidade, deleted_at from produtos';

        //commit the verication
        $product = $this->exist($queryBase, $parametersToAvoidDuplication, $isUniqueInputs);

        return !empty($product);
    }

    /**
     * @return array $products
     */
    public function get_products()
    {
        $queryBase = 'select id_produto as id, produto, quantidade, deleted_at as removido from produtos';
        $accepted_filters = [
            'quantidade' => ['param' => 'quantidade = :quantidade', 'operator' => ' and ', 'exclusive' => false],
            'id' => ['param' => 'id_produto = :id', 'operator' => ' and ', 'exclusive' => false],
            'produto' => ['param' => 'produto = :produto', 'operator' => ' and ', 'exclusive' => false],
            'deleted_at' => ['param' => 'deleted_at = :deleted_at', 'operator' => ' and ', 'exclusive' => false],
            'active' => ['param' => 'deleted_at is null', 'operator' => ' and ', 'exclusive' => false],
            'inactive' => ['param' => 'deleted_at is not null', 'operator' => ' and ', 'exclusive' => false],
        ];

        $productParameters = $this->get_product_parameters();

        [$filter_query,$queryParameters] = self::setQueryFilterSelect($productParameters, $accepted_filters);
        $query = $queryBase;
        
        if(!empty($filter_query)){
            $query = $queryBase . 'where' . $filter_query;
        }

        $conection = new database();
        $produto = $conection->EXE_QUERY($query, $queryParameters);

        return $this->responseSuccess($produto, 'product ok');
    }

    public function get_products_all()
    {
        $productParameters = $this->get_product_parameters();
        $queryBase = 'select id_produto, produto, quantidade from produtos';
        $accepted_filters = [
            'quantidade' => ['param' => 'quantidade = :quantidade', 'operator' => '', 'exclusive' => false],
        ];

        [$filter_query,$queryParameters] = self::setQueryFilterSelect($productParameters, $accepted_filters);
        $query = $queryBase;
        
        if(!empty($filter_query)){
            $query = $queryBase . 'where' . $filter_query;
        }

        $conection = new database();
        $produto = $conection->EXE_QUERY($query, $queryParameters);

        if ($produto) {
            $this->responseError('produto não encontrado');
        }
        return $this->responseSuccess($produto, 'product ok');
    }

    public function create_product()
    {
        $inputsRequired = ['produto' => ['min_4'], 'quantidade' => ['int']];
        $productParameters = $this->get_product_parameters();
        $parameterForExistenceQuery = ['produto' => ['param' => 'produto = :produto', 'operator' => '', 'exclusive' => false]];
        $QueryCreate = 'insert into produtos (produto, quantidade, created_at, updated_at) values(:produto, :quantidade, now(), now())';
        $queryBase = 'select id_produto, deleted_at from produtos';

        //checks that the parameters are set
        $productStatus = $this->issetParamasValidation($inputsRequired, $productParameters);
        if (!$productStatus['valid']) {
            return $this->responseError('existem parâmetros inválidos', $productStatus['erros']);
        }

        //check if exist register of inputs
        $dataToCheckExistence = array_intersect_key($productStatus['data'], $parameterForExistenceQuery);
        $product = $this->exist($queryBase, $dataToCheckExistence, $parameterForExistenceQuery);

        if (count($product) > 0) {
            return $this->responseError('O produto já está cadastrado');
        }

        // performs the insertion
        $paramsToQuery = $this->setQueryParams($productStatus['data']);
        $connection = new database();
        $result = $connection->EXE_NON_QUERY($QueryCreate, $paramsToQuery);
        if (!$result) {
            return $this->responseError('hove um error inesperado');
        }

        return $this->responseSuccess($result, 'inserction success');
    }

    public function update_product()
    {
        $queryUpdate = 'update produtos set produto = :produto, quantidade = :quantidade, updated_at = now() where id_produto = :id';
        $queryBase = 'select id_produto, deleted_at from produtos';
        $inputsRequired = ['id' => ['int'], 'produto' => ['min_4'], 'quantidade' => ['int']];
        $productParameters = $this->get_product_parameters();
        $parameterForExistenceQuery = [
            'produto' => ['param' => 'produto = :produto', 'operator' => ' and ', 'exclusive' => false],
            'id' => ['param' => 'id_produto <> :id', 'operator' => ' and ', 'exclusive' => false]
        ];

        //checks that the parameters are set
        $productStatus = $this->issetParamasValidation($inputsRequired, $productParameters);
        if (!$productStatus['valid']) {
            return $this->responseError('existem parâmetros inválidos', $productStatus['erros']);
        }

        //check if exist product registed
        $dataToCheckExistence = array_intersect_key($productStatus['data'], $parameterForExistenceQuery);
        $product = $this->exist($queryBase, $dataToCheckExistence, $parameterForExistenceQuery);
        // return $product;
        if (count($product) > 0) {
            return $this->responseError('O produto já está cadastrado');
        }

        // performs the insertion
        $paramsToQuery = $this->setQueryParams($productStatus['data']);
        $connection = new database();
        $result = $connection->EXE_NON_QUERY($queryUpdate, $paramsToQuery);

        if (!$result) {
            return $this->responseError('hove um error inesperado');
        }

        return $this->responseSuccess($result, 'inserction success');
    }

    public function destroy_product()
    {
        //
        $productParameters = $this->get_product_parameters();
        $queryDestruction = 'UPDATE produtos SET deleted_at=now() WHERE id_produto = :id';
        $queryBase = 'select id_produto, deleted_at from produtos';
        $inputsRequired = ['id' => ['int']];
        $checkExistInputs = [
            'id' => ['param' => 'id_produto = :id', 'operator' => ' and ', 'exclusive' => false],
            'active' => ['param' => 'deleted_at is null', 'operator' => ' and ', 'exclusive' => false]
        ];

        //checks that the parameters are set
        $productStatus = $this->issetParamasValidation($inputsRequired, $productParameters);
        $productStatus['data']['active'] = true;

        if (!$productStatus['valid']) {
            return $this->responseError('existem parâmetros inválidos', $productStatus['erros']);
        }


        //commits destruction
        $paramsToQuery = $this->setQueryParams($productStatus['data']);
        $connection = new database();
        $result = $connection->EXE_NON_QUERY($queryDestruction, $paramsToQuery);

        return $this->responseSuccess($$result, 'remove success');
    }
}
