<?php

namespace Api\inc;

trait Filter
{
    public static function setQueryFilterSelect(string $queryBase, $filters, $accepted_filters)
    {
        if (count($filters) < 1) {
            return [$queryBase, $filters];
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
            return [$queryBase, []];
        }
    
        if ($separateQueryParameter) {
            $query .= ')';
        }
    
        $query = $queryBase . ' where ' . $query;
        return [$query, $filters_to_query];
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

    protected function getFilter(string $params_to_filter): array
    {
        $pathern = '/(([^:;]+):([^;\s]+))(;\1|$)/';

        if (!isset($params_to_filter) || preg_match($pathern, $params_to_filter) == 0) {
            return [];
        }

        $filters = explode(';', $params_to_filter);
        $pathern = '/.+?:+?/';
        $filters_formated = [];

        foreach ($filters as $filter) {
            [$key,$value] = explode(':', $filter);
            $filters_formated[$key] = $value;
        }

        return $filters_formated;
    }
}
