<?php

function setQueryFilterSelect(string $queryBase, $filters, $accepted_filters)
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
$queryBase = 'select id, produto, quantidade, deleted_at from produtos';

$isUniqueInputs = [
    'produto' => ['param' => 'produto = :produto', 'operator' => ' or ', 'exclusive' => true],
    'quantidade' => ['param' => 'quantidade = :quantidade', 'operator' => ' and ', 'exclusive' => true],
    'id' => ['param' => 'id <> :id', 'operator' => ' and ', 'exclusive' => false]
];

$parma = ['id' => 128, 'produto' => 'j11 pro', 'quantidade' => 11];

// print_r(setQueryFilterSelect($queryBase, $parma, $isUniqueInputs)[0]);
// var_dump(setQueryFilterSelect($queryBase, $parma, $isUniqueInputs));
phpinfo();
