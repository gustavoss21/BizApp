<?php

namespace Api\inc;

if(!isset($allowedRoute)){
    die('<div style="color:red;">Rota não encontrada</div>');
}

class RouteBase{
    use Filter;
    use Response;

    protected $filters;
    protected $method;
    protected $user;
    protected $params;
    protected $endpoint;
    protected $requiredRoutePermissions;

        /**
     * define the clients parameters
     * @param object $class <p>Object passing by reference</p>
     * @param array $parameters <p>array with class attributes</p>
    */
    protected static function setClassParameters(object &$class, array $parameters)
    {
        foreach ($parameters as $parameter => $value) {
            $class->setParameter($parameter, $value);
    
        }
    }

    public function check_endpoint()
    {
        return method_exists($this, $this->endpoint);
    }

    public function setUser($username = '', $password = '')
    {
        $user = ['tokken' => $username, 'password' => $password];
        $this->setClassParameters($this->user, $user);
    }

    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * checked if the user is logged in 
    */
    public function autheticationRequired(){
        return $this->user->authenticate();
    }

    public function superAuthorizationRequired(){
        return $this->user->authenticate(true);
    }

    public function getRequiredMethod(){
        if($this->method != 'GET'){
            return Response::responseError('method is not permitted');

        }
        return Response::response([],'method is ok');
    }

    public function postRequiredMethod(){
        if($this->method != 'POST'){
            return Response::responseError('method is not permitted');

        }
        return Response::response([],'method is ok');
    }

    protected function CheckRoutePermission($endpoint){
        $status = Response::response([], 'User ok');

        //case not have required permission
        if(empty($this->requiredRoutePermissions[$endpoint])){
            return $status;
        }

        //call required permission
        foreach($this->requiredRoutePermissions[$endpoint] as $permissionRequired){
            $PermissionStatus = $this->$permissionRequired();
            if($PermissionStatus['error']){
                $status = $PermissionStatus;
            }
        }

        return $status;
    }

    public function route(string $endpoint)
    {
        //check access permissions
        $userStatus = $this->CheckRoutePermission($endpoint);

        if($userStatus['error']){
            return $userStatus;
        }
        
        return $this->$endpoint($this->params);
    }
}