<?php

namespace Api\inc;

if (!isset($allowedRoute)) {
    die('<div style="color:red;">Rota não encontrada</div>');
}

trait Validation
{
    /**
     * check if instance exists
     * @param string $queryBase <p>query select without argument where</p>
     * @param array $parameters <p>object attributes and their values</p>
     * @param array $accepted_filters <p>keys - attributes, values - query string</p>
     * @return PDOException|array|false <p>query result</p>
     */
    protected static function exist(string $queryBase, array $clientParameters, $accepted_filters)
    {
        [$filter_query,$queryParameters] = self::setQueryFilterSelect($clientParameters, $accepted_filters);
        $query = $queryBase;

        if (!empty($filter_query)) {
            $query = $queryBase . ' where ' . $filter_query;
        }

        $conection = new database();
        $result = $conection->EXE_QUERY($query, $queryParameters);

        return $result;
    }

    protected static function isValidParameter($parameter, $parameters)
    {
        $methods_validation = [];
        $methods_validation['min_4'] = fn ($nome) => preg_match('/.{4}/', $nome);
        $methods_validation['min_32'] = fn ($nome) => preg_match('/.{32}/', $nome);
        $methods_validation['min'] = fn ($numero) => preg_match('/^\d{11}$/', $numero);
        $methods_validation['int'] = fn ($numero) => preg_match('/^\d+$/', $numero);
        $methods_validation['not_null'] = fn ($numero) => preg_match('/^.+$/', $numero);
        $methods_validation['email'] = fn ($email) => filter_var($email, FILTER_VALIDATE_EMAIL);
        $methods_validation['fone'] = fn ($fone) => $this->validarTelefone($fone);

        foreach ($parameters as  $param_value) {
            $value_for_validation = $parameter;
            if (!$methods_validation[$param_value]($value_for_validation)) {
                return false;
            };
        }
        return true;
    }

    protected static function issetParamasValidation(array $params_required, array $parameters)
    {
        $validation = ['valid' => true, 'erros' => [], 'data' => []];

        foreach ($params_required as $parameter_query_name => $validatores) {
            if (!isset($parameters[$parameter_query_name]) or !$parameters[$parameter_query_name]) {
                $validation['valid'] = false;
                $validation['erros'][$parameter_query_name] = 'o parametro ' . $parameter_query_name . ' é requisitado!';
                continue;
            }
            $parameter = trim($parameters[$parameter_query_name]);
            $is_valid = self::isValidParameter($parameter, $validatores);
            if (!$is_valid) {
                $validation['valid'] = false;
                $validation['erros'][$parameter_query_name] = 'o parametro ' . $parameter_query_name . ' é invalido!';
                continue;
            }

            $validation['data'][$parameter_query_name] = $parameter;
        }

        return $validation;
    }

    public function validarTelefone($telefone)
    {
        $regex = '/^\+?(\d{2})?\s?\(?\d{2}\)?\s?\d{4,5}-?\d{4}$/';

        if (preg_match($regex, $telefone)) {
            return true;
        } else {
            return false;
        }
    }
}
