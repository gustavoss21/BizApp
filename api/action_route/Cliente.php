<?php

namespace Api\action_route;

require_once dirname(__FILE__, 2) . '/inc/Response.php';
require_once dirname(__FILE__, 2) . '/inc/Validation.php';
require_once dirname(__FILE__, 2) . '/inc/database.php';
require_once dirname(__FILE__, 2) . '/inc/Filter.php';

use Api\inc\Response;
use Api\inc\Validation;
use Api\inc\database;
use Api\inc\Filter;

class Cliente
{
    use Response;
    use Validation;
    use Filter;

    public function __construct(
        private int $id = 0,
        private string $nome = '',
        private string $email = '',
        private string $telefone = '',
        private string $deleted_at = '',
        private bool|null $active = false,
        private bool|null $inactive = false,
    ) {
    }

    /**
     * @return array <p>array with client attributes and their value
     */
    public function get_client_parameters()
    {
        return get_object_vars($this);
    }

    public function getParameter($parameter)
    {
        if (!isset($this->$parameter)) {
            return;
        }
        return $this->$parameter;
    }

    public function setParameter($parameter, $value)
    {
        if (!isset($this->{$parameter})) {
            return;
        }

        $this->$parameter = $value;
    }

    /**
     *check if there is already a registered customer
     *@return array <p>returned response error or success
     */
    public function check_client_exists(): bool
    {
        $isUniqueInputs = [
            'nome' => ['param' => 'nome = :nome', 'operator' => ' or ', 'exclusive' => true],
            'email' => ['param' => 'email = :email', 'operator' => ' and ', 'exclusive' => true],
            'id' => ['param' => 'id_cliente <> :id', 'operator' => ' and ', 'exclusive' => false]
        ];
        $clientParametersToValidation = $this->get_client_parameters();
        $parametersToAvoidDuplication = array_intersect_key($clientParametersToValidation, $isUniqueInputs);
        $queryBase = 'select id_cliente from clientes';

        //commit the verication
        $isExist = $this->exist($queryBase, $parametersToAvoidDuplication, $isUniqueInputs);


        return !empty($isExist);
    }

    public function get_clients()
    {
        $queryBase = 'select id_cliente as id, nome, email, telefone from clientes';
        $acceptedQueryFilters = [
            'id' => ['param' => 'id_cliente = :id', 'operator' => ' and ', 'exclusive' => false],
            'nome' => ['param' => 'nome = :nome', 'operator' => ' and ', 'exclusive' => false],
            'email' => ['param' => 'email = :email', 'operator' => ' and ', 'exclusive' => false],
            'deleted_at' => ['param' => 'deleted_at = :deleted_at', 'operator' => ' and ', 'exclusive' => false],
            'active' => ['param' => 'deleted_at is null', 'operator' => ' and ', 'exclusive' => false],
            'inactive' => ['param' => 'deleted_at is not null', 'operator' => ' and ', 'exclusive' => false],
        ];
        $client_parameters = $this->get_client_parameters();
        [$query,$filter_query] = self::setQueryFilterSelect($queryBase, $client_parameters, $acceptedQueryFilters);

        $conection = new database();
        $cliente = $conection->EXE_QUERY($query, $filter_query);

        return self::response($cliente, 'client ok');
    }

    public function create_client()
    {
        //inputs required
        $params_required = ['nome' => ['min_4'], 'email' => ['email'], 'telefone' => ['min']];

        //checks that the client parameters are set
        $client_parameter = $this->get_client_parameters();
        $clientStatus = $this->issetParamasValidation($params_required, $client_parameter);
        if (!$clientStatus['valid']) {
            return $this->responseError('existem parâmetros inválidos', $clientStatus['erros']);
        }

        // commit query
        $paramsToQuery = self::setQueryParams($clientStatus['data']);
        $connection = new database();
        $query = 'insert into clientes (nome, email, telefone, created_at, updated_at) values(:nome, :email, :telefone,now(),now())';
        $result = $connection->EXE_NON_QUERY($query, $paramsToQuery);

        return self::response($result, 'inserction success');
    }

    public function update_client()
    {
        $inputsRequired = ['id' => ['int'], 'email' => ['email'], 'telefone' => ['min']];
        $updateQuery = 'update clientes set email = :email, telefone = :telefone, updated_at = now() where id_cliente = :id';

        //checks that the parameters are set
        $clientStatus = $this->issetParamasValidation($inputsRequired, $this->get_client_parameters());

        if (!$clientStatus['valid']) {
            return $this->responseError('existem parâmetros inválidos', $clientStatus['erros']);
        }

        //commit client update
        $connection = new database();
        $paramsToQuery = $this->setQueryParams($clientStatus['data']);

        $result = $connection->EXE_NON_QUERY($updateQuery, $paramsToQuery);

        return self::response($result, 'update success');
    }

    public function destroy_client()
    {
        $inputsRequired = ['id' => ['int']];
        $queryDestroy = 'UPDATE clientes SET deleted_at=now() WHERE id_cliente = :id';

        //checks that the parameters are set
        $clientStatus = $this->issetParamasValidation($inputsRequired, $this->get_client_parameters());
        $clientStatus['data']['active'] = true;
        
        if (!$clientStatus['valid']) {
            return $this->responseError('existem parâmetros inválidos', $clientStatus['erros']);
        }

        // commit client destroy
        $paramsToQuery = $this->setQueryParams($clientStatus['data']);
        $connection = new database();
        $result = $connection->EXE_NON_QUERY($queryDestroy, $paramsToQuery);

        return self::response($result, 'remove success');
    }
}
