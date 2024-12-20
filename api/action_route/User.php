<?php

namespace Api\action_route;

if (!isset($allowedRoute)) {
    die('<div style="color:red;">Rota não encontrada</div>');
}
require_once dirname(__FILE__, 2) . '/inc/Response.php';
require_once dirname(__FILE__, 2) . '/inc/Validation.php';
require_once dirname(__FILE__, 2) . '/inc/database.php';
require_once dirname(__FILE__, 2) . '/inc/Filter.php';

use Api\inc\Response;
use Api\inc\Validation;
use Api\inc\database;
use Api\inc\Filter;

class User
{
    use Response;
    use Validation;
    use Filter;

    public function __construct(
        private int $id = 0,
        private string $nome = '',
        private string $email = '',
        private string $tokken = '',
        private bool $is_super_user = false,
        private string $password = '',
        private string $deleted_at = '',
        private bool|null $active = false,
        private bool|null $inactive = false,
        private bool|int $limit = false,
        private bool|string $fone_number = '',
        private bool|string $fone_area_code = '',
        private bool|string $identification_type = '',
        private bool|string $identification_number = '',
    ) {
    }

    public function getAllParameters()
    {
        return get_object_vars($this);
    }

    /**
     * @return array <p>array with client attributes and their value not null
     */
    public function getParameters()
    {
        return  array_filter($this->getAllParameters(), fn ($value) => !empty($value));
    }

    public function getMatchingParameters(array $array_filter)
    {
        $paramenters = $this->getAllParameters();

        $keys = array_intersect($array_filter, array_keys($paramenters));

        return  array_filter($paramenters, fn ($value, $key) => in_array($key, $keys), ARRAY_FILTER_USE_BOTH);
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
        if (!isset($this->{$parameter}) || empty($value)) {
            return;
        }

        $this->$parameter = trim($value);
    }

    /**
     *check if there is already a registered customer
     *@return array <p>returned response error or success
     */
    public function checkUserExists(): bool
    {
        $clientParametersToValidation = $this->getparameters();
        $queryBase = 'select id from authentication';
        $isUniqueInputs = [
            'nome' => ['param' => 'nome = :nome', 'operator' => ' or ', 'exclusive' => true],
            'tokken' => ['param' => 'tokken = :tokken', 'operator' => ' and ', 'exclusive' => true],
            'active' => ['param' => 'deleted_at is null', 'operator' => ' and ', 'exclusive' => false],
            'id' => ['param' => 'id <> :id', 'operator' => ' and ', 'exclusive' => false]
        ];

        $parametersToAvoidDuplication = array_intersect_key($clientParametersToValidation, $isUniqueInputs);

        //commit the verication
        $user = $this->exist($queryBase, $parametersToAvoidDuplication, $isUniqueInputs);

        return !empty($user);
    }

    /**
    *check if there is already a registered customer
    *@return array <p>returned response error or success
    */
    public function checkIsSuperUser(): bool
    {
        // change for superuser
        $this->setParameter('is_super_user', true);

        $clientParametersToValidation = $this->getparameters();
        $queryBase = 'select id from authentication';
        $isUniqueInputs = [
            'is_super_user' => ['param' => 'is_super_user is true', 'operator' => ' and ', 'exclusive' => false],
            'id' => ['param' => 'id = :id', 'operator' => ' and ', 'exclusive' => false]
        ];

        $parametersToAvoidDuplication = array_intersect_key($clientParametersToValidation, $isUniqueInputs);

        //commit the verication
        $user = $this->exist($queryBase, $parametersToAvoidDuplication, $isUniqueInputs);

        return !empty($user);
    }

    public function authenticate(bool $super_user = false)
    {
        $parameters_value = [':tokken' => $this->tokken];
        $query = 'select id, passwd, nome  from authentication where deleted_at is null and tokken = :tokken';
        $query .= $super_user ? ' and is_super_user is not false' : '';

        $conection = new database();
        $user = $conection->EXE_QUERY($query, $parameters_value);

        if (!$user) {
            return $this->responseError('Você não tem autorização para fazer essa ação');
        }

        if (!password_verify($this->password, $user[0]['passwd'])) {
            return $this->responseError('Você não tem autorização para fazer essa ação');
        }

        unset($user[0]['passwd']);
        return $this->responseSuccess($user, 'User ok');
    }

    public function get_users()
    {
        $filters = $this->getparameters();
        $queryBase = 'select id, nome,tokken, created_at as criado, deleted_at as removido from authentication';
        $accepted_filters = [
            'id' => ['param' => 'id = :id', 'operator' => ' and ', 'exclusive' => false],
            'nome' => ['param' => 'nome = :nome', 'operator' => ' and ', 'exclusive' => false],
            'deleted_at' => ['param' => 'deleted_at = :deleted_at', 'operator' => ' and ', 'exclusive' => false],
            'active' => ['param' => 'deleted_at is null', 'operator' => ' and ', 'exclusive' => false],
            'inactive' => ['param' => 'deleted_at is not null', 'operator' => ' and ', 'exclusive' => false],
            'limit' => ['param' => 'ORDER BY id LIMIT :limit', 'operator' => ' and ', 'exclusive' => false],
        ];

        $conection = new database();

        [$filter_query,$queryParameters] = self::setQueryFilterSelect($filters, $accepted_filters);
        $query = $queryBase;

        if (!empty($filter_query)) {
            $query = $queryBase . 'where' . $filter_query;
        }

        $users = $conection->EXE_QUERY($query, $queryParameters);

        return $this->responseSuccess($users, 'Usuários ok');
    }

    public function search_user()
    {
        $filters = $this->getparameters();
        $queryBase = 'select id, nome,tokken, email, created_at as criado, deleted_at, identification_type, identification_number,fone_number, fone_area_code from authentication';
        $accepted_filters = [
            'id' => ['param' => 'id = :id', 'operator' => ' and ', 'exclusive' => false],
            'nome' => ['param' => 'nome = :nome', 'operator' => ' and ', 'exclusive' => false],
            'tokken' => ['param' => 'tokken = :tokken', 'operator' => ' and ', 'exclusive' => false],
            'email' => ['param' => 'email = :email', 'operator' => ' and ', 'exclusive' => false],
            'deleted_at' => ['param' => 'deleted_at = :deleted_at', 'operator' => ' and ', 'exclusive' => false],
            'active' => ['param' => 'deleted_at is null', 'operator' => ' and ', 'exclusive' => false],
            'inactive' => ['param' => 'deleted_at is not null', 'operator' => ' and ', 'exclusive' => false],
        ];

        $conection = new database();

        [$filter_query,$queryParameters] = self::setQueryFilterSelect($filters, $accepted_filters);
        $query = $queryBase;

        if (!empty($filter_query)) {
            $query = $queryBase . ' where ' . $filter_query;
        }

        if ($this->getParameter('limit')) {
            $query .= ' ORDER BY id LIMIT 1';
        }

        $user = $conection->EXE_QUERY($query, $queryParameters);

        if (count($user) > 0) {
            //set new parameters
            foreach ($user[0] as $usKey => $usValue) {
                $this->setParameter($usKey, $usValue);
            };
        }

        return $this->responseSuccess($user, 'Usuários ok');
    }

    public function create_user()
    {
        $inputsRequired = ['nome' => ['min_4'], 'tokken' => ['min_32'], 'password' => ['min_32'], 'email' => ['email'], 'fone_area_code' => [], 'fone_number' => [], 'identification_type' => [], 'identification_number' => []];
        $query = 'insert into authentication 
                (nome, email, fone_area_code, fone_number, identification_type, identification_number, tokken, passwd, created_at, updated_at)
                values(:nome, :email,:fone_area_code,:fone_number,:identification_type,:identification_number, :tokken, :password, now(), now())';

        //checks that the parameters are set
        $params_data = self::issetParamasValidation($inputsRequired, $this->getparameters());

        if (!$params_data['valid']) {
            return $this->responseError('existem parâmetros inválidos', $params_data['erros']);
        }

        //encrypt password
        $params_data['data']['password'] = password_hash($params_data['data']['password'], PASSWORD_DEFAULT);

        $paramsToQuery = $this->setQueryParams($params_data['data']);
        $connection = new database();
        $result = $connection->EXE_NON_QUERY($query, $paramsToQuery);

        return $this->responseSuccess($result, 'inserction success');
    }

    public function updateUser()
    {
        $inputsRequired = ['id' => ['int'], 'nome' => ['min_4']];
        $query = 'update authentication set nome = :nome where id = :id';

        if (isset($this->params['tokken'])) {
            $inputsRequired = ['id' => ['int'], 'nome' => ['min_4'], 'tokken' => ['min_32'], 'password' => ['min_32']];
            $query = 'update authentication set nome = :nome, tokken = :tokken, passwd = :password where id = :id';
        }

        //checks that the parameters are set
        $params_data = self::issetParamasValidation($inputsRequired, $this->getparameters());

        if (!$params_data['valid']) {
            return $this->responseError('existem parâmetros inválidos', $params_data['erros']);
        }

        $paramsToQuery = $this->setQueryParams($params_data['data']);

        $connection = new database();
        $result = $connection->EXE_NON_QUERY($query, $paramsToQuery);

        return $this->responseSuccess($result, 'update success');
    }

    public function activeUser()
    {
        $inputsRequired = ['id' => ['int']];
        //checks that the parameters are set
        $params_data = $this->issetParamasValidation($inputsRequired, $this->getparameters());
        $params_data['data']['inactive'] = true;
        $params_data['data']['is_super_user'] = true;

        if (!$params_data['valid']) {
            return $this->responseError('existem parâmetros inválidos', $params_data['erros']);
        }

        // //check if exist register of inputs
        // $check_exist_inputs = [
        //     'id' => ['param' => 'id = :id', 'operator' => ' and ', 'exclusive' => false],
        //     'active' => ['param' => 'deleted_at is null', 'operator' => ' and ', 'exclusive' => false],
        // ];

        // $dataToCheckExistence = array_intersect_key($params_data['data'], $check_exist_inputs);
        // $queryBase = 'select id from authentication';
        // $isExist = $this->exist($queryBase, $dataToCheckExistence, $check_exist_inputs);

        // if (count($isExist) <= 0) {
        //     return $this->responseError('Usuário não encontrado ou não pode ser removido, tente mais tarde!');
        // }

        $paramsToQuery = $this->setQueryParams($params_data['data']);
        $query = 'UPDATE authentication SET deleted_at=null WHERE id = :id';

        $connection = new database();
        $result = $connection->EXE_NON_QUERY($query, $paramsToQuery);

        return $this->responseSuccess($result, 'remove success');
    }

    public function destroy_user()
    {
        $response_authenticate = $this->authenticate();

        if ($response_authenticate['error']) {
            //return $response_authenticate;
        }
        //inputs required
        $params = ['id' => ['int']];
        //checks that the parameters are set
        $params_data = $this->issetParamasValidation($params, $this->getparameters());
        $params_data['data']['active'] = true;
        $params_data['data']['is_super_user'] = true;

        if (!$params_data['valid']) {
            return $this->responseError('existem parâmetros inválidos', $params_data['erros']);
        }

        //check if exist register of inputs
        $check_exist_inputs = [
            'id' => ['param' => 'id = :id', 'operator' => ' and ', 'exclusive' => false],
            'is_super_user' => ['param' => 'is_super_user is false', 'operator' => ' and ', 'exclusive' => false],
            'active' => ['param' => 'deleted_at is null', 'operator' => ' and ', 'exclusive' => false],
        ];

        $dataToCheckExistence = array_intersect_key($params_data['data'], $check_exist_inputs);
        $queryBase = 'select id, deleted_at from authentication';
        $isExist = $this->exist($queryBase, $dataToCheckExistence, $check_exist_inputs);

        if (count($isExist) <= 0) {
            return $this->responseError('Usuário não encontrado ou não pode ser removido, tente mais tarde!');
        }

        $paramsToQuery = $this->setQueryParams($params_data['data']);
        $query = 'UPDATE authentication SET deleted_at=now() WHERE id = :id';
        // return $this->responseError('Usuá', [$query, $paramsToQuery]);

        $connection = new database();
        $result = $connection->EXE_NON_QUERY($query, $paramsToQuery);
        if (!$result) {
            return $this->responseError('houve um erro inesperado');
        }

        return $this->responseSuccess($result, 'remove success');
    }
}
