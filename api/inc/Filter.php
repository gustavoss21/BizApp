<?php

namespace Api\inc;

if (!isset($allowedRoute)) {
    die('<div style="color:red;">Rota não encontrada</div>');
}

trait Filter
{
    public static function setQueryFilterSelect($filters, $accepted_filters)
    {
        if (count($filters) < 1) {
            return ['queryWhereSlice' => '', 'parameterQuery' => ''];
        }

        $query = '';
        $filters_to_query = [];
        $keypast = '';
        $are_not_filters_for_consultation = ['active', 'inactive', 'is_super_user'];
        $separateQueryParameter = false;

        foreach ($filters as $key => $filter) {
            if (!isset($accepted_filters[$key]) || empty($filter)) {
                continue;
            }

            //adicona o operador da query
            if ($keypast) {
                $query .= $accepted_filters[$keypast]['operator'];
            }

            $paramater = $accepted_filters[$key]['param'];
            $keypast = $key;

            if ($accepted_filters[$key]['exclusive'] && !$separateQueryParameter) {
                $paramater = '(' . $paramater;
                $separateQueryParameter = true;
            }

            if (!$accepted_filters[$key]['exclusive'] && $separateQueryParameter) {
                $paramater = $paramater . ')';
                $separateQueryParameter = false;
            }

            $query .= $paramater;

            if (in_array($key, $are_not_filters_for_consultation)) {
                continue;
            }

            $filters_to_query[':' . $key] = $filter;
        }

        if (!$query) {
            return ['queryWhereSlice' => '', 'parameterQuery' => ''];
        }

        if ($separateQueryParameter) {
            $query .= ')';
        }

        return [$query, $filters_to_query];
    }

    /**
     * @param array $arr <p>key and value of object</p>
     * @return string
     */
    protected static function setQueryInsert($arr){
        $a = [
            ];
            
            foreach (array_keys($arr) as $k) {
                $a[] = $k . ' = :' . $k;
            }
            
        return implode(', ', $a);
    }

    protected static function setQueryParams(array $values_to_query)
    {
        $parameters_fomated = [];
        $are_not_params_for_consultation = ['active', 'inactive', 'is_super_user'];
        foreach ($values_to_query as $key => $filter) {
            if (in_array($key, $are_not_params_for_consultation)) {
                continue;
            }
            $parameters_fomated[':' . $key] = $filter;
        }

        return $parameters_fomated;
    }

    protected static function setPartialQueryWhere($values_to_query){
        $parameters_fomated = [];
        $sliceQueryWhere = [];
        $are_not_params_for_consultation = ['active', 'inactive', 'is_super_user'];

        foreach ($values_to_query as $key => $filter) {
            if (in_array($key, $are_not_params_for_consultation)) {
                continue;
            }
            $parameters_fomated[':' . $key] = $filter;
            $sliceQueryWhere[] = ':' . $key;
        }

        return [implode(',',$sliceQueryWhere), $parameters_fomated];

    }

    protected function getFilter(string $params_to_filter): array
    {
        $filters = explode(';', $params_to_filter);
        $filters_formated = [];

        foreach ($filters as $filter) {
            @[$key, $value] = explode(':', $filter);

            if (empty($key) || empty($value)) {
                continue;
            }

            $filters_formated[$key] = $value;
        }

        return $filters_formated;
    }
}
