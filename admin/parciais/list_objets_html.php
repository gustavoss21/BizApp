<?php
if(!isset($allowedRoute)){
    die('<div style="color:red;">Rota não encontrada</div>');
}

// open tags
$param_link = 'nome';
$html = <<<HTML

    <h1 style="text-align: center;color:rgb(148 11 11)">
        $subtitle
        <a href="{$link_create}">
            <span class="content-add-icon">
                <div class="add icon-bar-x"></div>
                <div class="add icon-bar-y"></div>
            </span>
        </a>
    </h1>
    <!-- filter action -->
    <div> 
        <span>filtro:</span>
    <div class="filter_search">
        <div {$filterActive['all']}><a href="$link_base">todos</a></div>
        <div {$filterActive['active']}><a href="$link_base?filter=active:true">ativos</a></div>
        <div {$filterActive['inactive']}><a href="$link_base?filter=inactive:true">inativos</a></div>
    </div>
    <div>
    HTML;
if (!$data) {
    return $html .= '<div style="color:red;text-align:center;">nenhum usuário encontrodo</div>';
}
    
// main body / set data

//set header table
$html .= ' <table class=table>
            <thead>
                <tr>';
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
            $url = $link_update . $value->id;
            $set_html_value = '<a href="' . $url . '">' . $set_html_value . '</a>';
        }
        //set data
        $html .= "<td>{$set_html_value}</td>";
    }

    //call method deactive item
    $actions = <<<HTML
        <td>
            <a href="$link_delete{$value->id}">desativar</a>
        </td>
     HTML;
    //call method active item
    if ($value->removido) {
        $actions = <<<HTML
        <td>
            <a href="$link_active{$value->id}">Ativar</a>
        </td>
     HTML;
    }
    $html .= $actions;


    $html .= '</tr>';

        
}


// close tags
$html .= <<<HTML
    
            </tbody>
        </table>
    </div>
HTML;

return $html;
