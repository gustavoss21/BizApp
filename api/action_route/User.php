<?php

namespace Api\action_route;

require_once dirname(__FILE__,2) . '/inc/Response.php';

// use Api\inc\Filter;
use Api\inc\Response;
// use Api\inc\Validation;
use Api\inc\database;

class User
{
    use Response;
    // use Filter;
    // use Validation;

    public function __construct(
        private int $id = 0,
        private string $name = '',
        private string $tokken = '',
        private string $password = ''
    ) {
    }

    public function get_client_parameters(){
        return get_object_vars($this);        
    }

    public function getParameter($parameter){
        if(!isset($this->$parameter)){
            return;
        }
        return $this->$parameter;        
    }

    public function setParameter($parameter,$value){
        if(!isset($this->$parameter)){
            return;
        }

        $this->$parameter = $value;
    }

    public function check_client_exists(){}

    public function authenticate(bool $super_user = false)
    {
        $parameters_value = [':tokken' => $this->tokken];
        $query = 'select id, passwd, nome  from authentication where deleted_at is null and tokken = :tokken';
        $query .= $super_user ? ' and is_super_user is not false' : '';
        // return [$query, $parameters_value];
        $conection = new database();
        $user = $conection->EXE_QUERY($query, $parameters_value);
        if (!$user) {
            return $this->responseError('Você não tem autorização para fazer essa ação');
        }

        if (!password_verify($this->password, $user[0]['passwd'])) {
            return $this->responseError('Você não tem autorização para fazer essa ação');
        }

        unset($user[0]['passwd']);
        return $this->response($user, 'User ok');
    }

    // public function get_users()
    // {
    //     $filters = $this->getFilter(' get["filter"]');

    //     $accepted_filters = [
    //         'id' => ['param' => 'id = :id', 'operator' => ' and ', 'exclusive'=>false],
    //         'nome' => ['param' => 'nome = :nome', 'operator' => ' and ', 'exclusive'=>false],
    //         'deleted_at' => ['param' => 'deleted_at = :deleted_at', 'operator' => ' and ', 'exclusive'=>false],
    //         'active' => ['param' => 'deleted_at is null', 'operator' => ' and ', 'exclusive'=>false],
    //         'inactive' => ['param' => 'deleted_at is not null', 'operator' => ' and ', 'exclusive'=>false],
    //     ];

    //     $queryBase = 'select id, nome,tokken, created_at as criado, deleted_at as removido from authentication';

    //     [$query,$filter_query] = $this->setQueryFilterSelect($queryBase, $filters, $accepted_filters);
    //     // return $this->responseError('Usuários não encontrado',[$query,$this->params]);

    //     $conection = new database();

    //     $users = $conection->EXE_QUERY($query, $filter_query);
    //     // return $this->responseError('Usuários não encontrado',[$filters]);

    //     return $this->response($users, 'Usuários ok');
    // }

    // public function create_user()
    // {
    //     $response_authenticate = $this->authenticate(true);

    //     if ($response_authenticate['error']) {
    //         return $response_authenticate;
    //     }

    //     //inputs required and validators
    //     $params = ['nome' => ['min_4'], 'tokken' => ['min_32'], 'password' => ['min_32']];

    //     //checks that the parameters are set
    //     $params_data = self::issetParamasValidation($params, ['get[filter]']);

    //     if (!$params_data['valid']) {
    //         return $this->responseError('existem parâmetros inválidos', $params_data['erros']);
    //     }

    //     //check if exist register of inputs
    //     $parameterForExistenceQuery = [
    //         'nome' => ['param' => 'nome = :nome', 'operator' => ' or ', 'exclusive'=>true],
    //         'tokken' => ['param' => 'tokken = :tokken', 'operator' => ' and ', 'exclusive'=>true],
    //         'active' => ['param' => 'deleted_at is null', 'operator' => ' and ', 'exclusive'=>false],
    //     ];

    //     $dataToCheckExistence = array_intersect_key($params_data['data'], $parameterForExistenceQuery);
    //     $queryBase = 'select id, nome, deleted_at from authentication';

    //     $isExist = $this->exist($queryBase, $dataToCheckExistence, $parameterForExistenceQuery);

    //     if (count($isExist) > 0) {
    //         return $this->responseError('o nome ou username já está cadastrado');
    //     }

    //     $params_data['data']['password'] = password_hash($params_data['data']['password'], PASSWORD_DEFAULT);
    //     $paramsToQuery = $this->setQueryParams($params_data['data']);
    //     $query = 'insert into authentication (nome, tokken, passwd, created_at, updated_at) values(:nome, :tokken, :password, now(), now())';

    //     $connection = new database();
    //     $result = $connection->EXE_NON_QUERY($query, $paramsToQuery);

    //     if (!$result) {
    //         return $this->responseError('hove um error inesperado');
    //     }

    //     return $this->response($result, 'inserction success');
    // }

    // public function updateUser()
    // {
    //     $response_authenticate = $this->authenticate();

    //     if ($response_authenticate['error']) {
    //         return $response_authenticate;
    //     }
    //     //inputs required
    //     $params = ['id' => ['int'], 'nome' => ['min_4']];
    //     $parameterForExistenceQuery = [
    //         'nome' => ['param' => 'nome = :nome', 'operator' => ' or ', 'exclusive'=>false],
    //         'id' => ['param' => 'id <> :id', 'operator' => ' and ', 'exclusive'=>false]
    //     ];
    //     $query = 'update authentication set nome = :nome where id = :id';

    //     if (isset($this->params['tokken'])) {
    //         //inputs required
    //         $params = ['id' => ['int'], 'nome' => ['min_4'], 'tokken' => ['min_32'], 'password' => ['min_32']];
    //         $query = 'update authentication set nome = :nome, tokken = :tokken, passwd = :password where id = :id';
    //     }

    //     //checks that the parameters are set
    //     $params_data = self::issetParamasValidation($params, ['get[filter]']);
    //     // return $this->responseError($params_data);

    //     if (!$params_data['valid']) {
    //         return $this->responseError('existem parâmetros inválidos', $params_data['erros']);
    //     }

    //     //check if exist client registed

    //     $dataToCheckExistence = array_intersect_key($params_data['data'], $parameterForExistenceQuery);
    //     $queryBase = 'select id, nome, deleted_at from authentication';

    //     $isExist = $this->exist($queryBase, $dataToCheckExistence, $parameterForExistenceQuery);

    //     if (count($isExist) > 0) {
    //         return $this->responseError('email ou nome já está cadastrado');
    //     }

    //     $paramsToQuery = $this->setQueryParams($params_data['data']);
    //     // return $this->response([$paramsToQuery,$query], 'update success');

    //     $connection = new database();
    //     $result = $connection->EXE_NON_QUERY($query, $paramsToQuery);
    //     if (!$result) {
    //         return $this->responseError('hove um error inesperado');
    //     }

    //     return $this->response($result, 'update success');
    // }

    // public function activeUser()
    // {
    //     $response_authenticate = $this->authenticate();

    //     if ($response_authenticate['error']) {
    //         return $response_authenticate;
    //     }
    //     //inputs required
    //     $params = ['id' => ['int']];
    //     //checks that the parameters are set
    //     $params_data = $this->issetParamasValidation($params, ['get[filter]']);
    //     $params_data['data']['inactive'] = true;
    //     $params_data['data']['is_super_user'] = true;
    //     if (!$params_data['valid']) {
    //         return $this->responseError('existem parâmetros inválidos', $params_data['erros']);
    //     }

    //     //check if exist register of inputs
    //     $check_exist_inputs = [
    //         'id' => ['param' => 'id = :id', 'operator' => ' and ', 'exclusive'=>false],
    //         'is_super_user' => ['param' => 'is_super_user is false', 'operator' => ' and ', 'exclusive'=>false],
    //         'active' => ['param' => 'deleted_at is null', 'operator' => ' and ', 'exclusive'=>false],
    //     ];

    //     $dataToCheckExistence = array_intersect_key($params_data['data'], $check_exist_inputs);
    //     $queryBase = 'select id, deleted_at from authentication';
    //     $isExist = $this->exist($queryBase, $dataToCheckExistence, $check_exist_inputs);

    //     if (count($isExist) <= 0) {
    //         return $this->responseError('Usuário não encontrado ou não pode ser removido, tente mais tarde!');
    //     }

    //     $paramsToQuery = $this->setQueryParams($params_data['data']);
    //     $query = 'UPDATE authentication SET deleted_at=null WHERE id = :id';
    //     // return $this->responseError('Usuá', [$query, $paramsToQuery]);

    //     $connection = new database();
    //     $result = $connection->EXE_NON_QUERY($query, $paramsToQuery);
    //     if (!$result) {
    //         return $this->responseError('houve um erro inesperado');
    //     }

    //     return $this->response($result, 'remove success');
    // }

    // public function destroy_user()
    // {
    //     $response_authenticate = $this->authenticate();

    //     if ($response_authenticate['error']) {
    //         //return $response_authenticate;
    //     }
    //     //inputs required
    //     $params = ['id' => ['int']];
    //     //checks that the parameters are set
    //     $params_data = $this->issetParamasValidation($params, ['get[filter]']);
    //     $params_data['data']['active'] = true;
    //     $params_data['data']['is_super_user'] = true;
    //     if (!$params_data['valid']) {
    //         return $this->responseError('existem parâmetros inválidos', $params_data['erros']);
    //     }

    //     //check if exist register of inputs
    //     $check_exist_inputs = [
    //         'id' => ['param' => 'id = :id', 'operator' => ' and ', 'exclusive'=>false],
    //         'is_super_user' => ['param' => 'is_super_user is false', 'operator' => ' and ', 'exclusive'=>false],
    //         'active' => ['param' => 'deleted_at is null', 'operator' => ' and ', 'exclusive'=>false],
    //     ];

    //     $dataToCheckExistence = array_intersect_key($params_data['data'], $check_exist_inputs);
    //     $queryBase = 'select id, deleted_at from authentication';
    //     $isExist = $this->exist($queryBase, $dataToCheckExistence, $check_exist_inputs);

    //     if (count($isExist) <= 0) {
    //         return $this->responseError('Usuário não encontrado ou não pode ser removido, tente mais tarde!');
    //     }

    //     $paramsToQuery = $this->setQueryParams($params_data['data']);
    //     $query = 'UPDATE authentication SET deleted_at=now() WHERE id = :id';
    //     // return $this->responseError('Usuá', [$query, $paramsToQuery]);

    //     $connection = new database();
    //     $result = $connection->EXE_NON_QUERY($query, $paramsToQuery);
    //     if (!$result) {
    //         return $this->responseError('houve um erro inesperado');
    //     }

    //     return $this->response($result, 'remove success');
    // }
}
