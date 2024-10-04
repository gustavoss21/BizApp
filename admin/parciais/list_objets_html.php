<?php

// open tags
$cont = 0;
$param_hidden = 'id';
$param_link = 'nome';

if(!$data){
    return $body = '<div style="color:red;text-align:center;">nenhum '.$title. ' cadastrado';
}

$html = <<<HTML

    <h1 style="text-align: center;color:rgb(148 11 11)">$subtitle</h1>
    <div> 
        <span>filtro:</span>
    <div class="filter_search">
        <div {$filterActive['all']}><a href="$link_base">todos</a></div>
        <div {$filterActive['active']}><a href="$link_base?filter=active:true">ativos</a></div>
        <div {$filterActive['inactive']}><a href="$link_base?filter=inactive:true">inativos</a></div>
    </div>
    <div>
        <table class=table>
            <thead>
                <tr>
    HTML;

// main body / set data

//set header table
$attribute_object = array_keys((array) $data[0]);
foreach ($attribute_object as $key) {
    $html .= <<<HTML
        <th>{$key}</th>
    HTML;
}

//set body table
$html .= '</tr></thead><tbody>';

foreach ($data as $value) {
    $html .= '<tr>';
    foreach ($attribute_object as $key) {
        $set_html_value = $value->$key;
        // set url table link item
        if ($key == $param_link) {
            $url = $link_base . '/?filter=' . $param_hidden . ':' . $value->$param_hidden;
            $set_html_value = '<a href="' . $url . '">' . $set_html_value . '</a>';
        }

        $html .= "<td>{$set_html_value}</td>";
        $cont++;
    }

    //methods and actions
    $html .= <<<HTML
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