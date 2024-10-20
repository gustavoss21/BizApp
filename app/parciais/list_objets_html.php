<?php

if(!isset($allowedRoute)){
    die('<div style="color:red;">Rota não encontrada</div>');
}

// open tags
$cont = 0;
$param_hidden = 'id';
$param_link = ['nome', 'produto'];

if (!$data) {
    return $body = '<div style="color:red;text-align:center;">nenhum ' . $title . ' cadastrado';
}
// printDebug($data);
//table head / set icon add
$html = <<<HTML

    <h1 style="text-align: center;color:rgb(148 11 11)">$subtitle 
        <a href="{$link_create}">
            <span class="content-add-icon">
                <div class="add icon-bar-x"></div>
                <div class="add icon-bar-y"></div>
            </span>
        </a>
    </h1>
    <div>
        <table class=table>
            <thead>
                <tr>
    HTML;

// main body / set data

//set header table
$attribute_object = array_keys((array) $data[0]);
foreach ($attribute_object as $key) {
    if ($key === $param_hidden) {
        continue;
    }

    $html .= <<<HTML
        <th>{$key}</th>
    HTML;
}

//set body table
$html .= '</tr></thead><tbody>';

foreach ($data as $value) {
    $html .= '<tr>';
    foreach ($attribute_object as $key) {
        if ($key === $param_hidden) {
            $param_hidden = $key;
            continue;
        }

        $set_html_value = $value->$key;

        // set url table link item
        if (in_array($key, $param_link)) {
            $url = $link_base . '/?filter=' . $param_hidden . ':' . $value->$param_hidden;
            $set_html_value = '<a href="' . $url . '">' . $set_html_value . '</a>';
        }

        $html .= "<td>{$set_html_value}</td>";
        $cont++;
    }

    //methods and actions
    $html .= <<<HTML
        <td>
            <a href="$link_update{$value->$param_hidden}">atualizar</a>
        </td>
        <td>
            <a href="$link_delete{$value->$param_hidden}">remover</a>
        </td>
     HTML;

    $cont = 0;
    $html .= '</tr>';
}

// close tags
$html .= <<<HTML
    
            </tbody>
        </table>
    </div>
HTML;

return $html;
