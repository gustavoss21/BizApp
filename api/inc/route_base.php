<?php

namespace Api\inc;

require_once 'action_route/User.php';
require_once 'inc/Filter.php';
require_once 'inc/Response.php';
require_once 'action_route/Cliente.php';
require_once 'action_route/Product.php';
require_once 'controller/PaymentController.php';
require_once 'controller/UserController.php';

use Api\inc\Filter;
use Api\inc\Response;
use Api\action_route\Cliente;
use Api\action_route\Product;
use Exception;

use Api\controller\PaymentController;
use Api\controller\ProductController;

use Api\action_route\User;

if(!isset($allowedRoute)){
    die('<div style="color:red;">Rota não encontrada</div>');
}

abstract class AbstractRoute{
    use Filter;
    use Response;
    

    protected $filters;
    protected $method;
    protected $user;
    protected $response_method = '';
    protected $routes;

    abstract protected function setRoutes();

    public function __construct(protected $params, protected $endpoint)
    {
        $this->user = new User();
        $this->setRoutes();
    }

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

    public function authorizateOnlyActive(){
        $isAutheticate = $this->autheticationRequired();
        $parametersDenied = ['deleted_at', 'inactive'];
        
        if(!$isAutheticate['error']){
            return self::responseSuccess([], 'request authorizated');
        }

        if(array_intersect_key($parametersDenied,$this->params)){
            return self::responseError('request unauthorized');

        }

        return self::responseSuccess([], 'request authorizated');

    }

    public function authorizationOnlyOne(){
        
    }

    public function check_endpoint()
    {
        return array_key_exists($this->endpoint, $this->routes);
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
        return Response::responseSuccess([],'method is ok');
    }

    public function postRequiredMethod(){
        if($this->method != 'POST'){
            return Response::responseError('method is not permitted');

        }
        return Response::responseSuccess([],'method is ok');
    }

    protected function CheckRoutePermission($Validationfuctions){
        $status = $this->responseSuccess([], 'User ok');

        //call required permission
        foreach($Validationfuctions as $permissionRequired){
            $PermissionStatus = $this->$permissionRequired();
            if($PermissionStatus['error']){
                $status = $PermissionStatus;
            }
        }

        return $status;
    }

    public function route(string $endpoint)
    {
        $controller = $this->routes[$endpoint]['controller'];
        $method = $this->routes[$endpoint]['method'];
        $functionsRequired = $this->routes[$endpoint]['Required'];

        //check access permissions
        $userStatus = $this->CheckRoutePermission($functionsRequired);

        if($userStatus['error']){
            return $userStatus;
        }

        $classCotroller = new $controller($this->params);
        
        return $classCotroller->$method();
    }


}