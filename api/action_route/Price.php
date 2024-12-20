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

class Price
{
    use Response;
    use Validation;
    use Filter;

    public const PRICE_DEFAULT_ID = 1;

    public function __construct(
        private $id=0,
        private $price='',
        private $product='',
        private $description = ''
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
     * return one price. If not founded id, return price default
     */
    public function getPrice()
    {
        $filters = $this->getparameters();

        if (!isset($filters['id'])) {
            {
                $filters['id'] = self::PRICE_DEFAULT_ID;
            }
        }

        $queryBase = 'select id, product, description, price from prices';

        $accepted_filters = [
            'id' => ['param' => 'id = :id', 'operator' => ' and ', 'exclusive' => false],
        ];

        $conection = new database();

        [$filter_query,$queryParameters] = self::setQueryFilterSelect($filters, $accepted_filters);
        $query = $queryBase;

        if (!empty($filter_query)) {
            $query = $queryBase . ' where ' . $filter_query;
        }

        $query .= ' ORDER BY id LIMIT 1';

        $price = $conection->EXE_QUERY($query, $queryParameters);

        if (count($price) > 0) {
            //set new parameters
            foreach ($price[0] as $pcKey => $pcValue) {
                $this->setParameter($pcKey, $pcValue);
            };
        }

        return $this->responseSuccess($price, 'Usuários ok');
    }

    public function createPrice()
    {
        $inputsRequired = ['product' => ['min_4'], 'price' => ['']];
        $query = 'insert into prices (product,price) values (:product,:price)';

        //checks that the parameters are set
        $params_data = self::issetParamasValidation($inputsRequired, $this->getparameters());

        if (!$params_data['valid']) {
            return $this->responseError('existem parâmetros inválidos', $params_data['erros']);
        }

        $paramsToQuery = $this->setQueryParams($params_data['data']);
        $connection = new database();
        $result = $connection->EXE_NON_QUERY($query, $paramsToQuery);

        return $this->responseSuccess($result, 'inserction success');
    }

    public function updatePrice()
    {
        $inputsRequired = ['product' => ['min_4'], 'price' => ['']];
        $query = 'update prices set product = :product, price = :price where id = :id';

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

    public function destroyPrice()
    {
        //inputs required
        $params = ['id' => ['int']];

        //checks that the parameters are set
        $params_data = $this->issetParamasValidation($params, $this->getparameters());

        if (!$params_data['valid']) {
            return $this->responseError('existem parâmetros inválidos', $params_data['erros']);
        }

        $paramsToQuery = $this->setQueryParams($params_data['data']);
        $query = 'DELETE FROM prices WHERE id = :id';

        $connection = new database();
        $result = $connection->EXE_NON_QUERY($query, $paramsToQuery);
        if (!$result) {
            return $this->responseError('houve um erro inesperado');
        }

        return $this->responseSuccess($result, 'remove success');
    }
}
