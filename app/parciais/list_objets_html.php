<?php
// open tags
$cont = 0;
$params_hidden = ['id_cliente', 'id_produto'];
$param_hidden = '';
$param_link = ['nome','produto'];

$html = <<<HTML

    <h1 style="text-align: center">$subtitle</h1>
    <div>
        <table class=table>
            <thead>
                <tr>
    HTML;

// main body / set data 

//set header table
$attribute_object = array_keys((array) $data[0]);
foreach ( $attribute_object as $key) {
    if(in_array($key,$params_hidden))continue;

    $html .= <<<HTML
        <th>{$key}</th>
    HTML;
}

//set body table
$html .= '</tr></thead><tbody>';

foreach ($data as $value) {
    $html .= '<tr>';
    foreach ( $attribute_object as $key) {
        if (in_array($key, $params_hidden)) {
            $param_hidden = $key;
            continue;
        }

        $set_html_value = $value->$key;
        // set url link table
        if(in_array($key,$param_link)){
            $url = API_BASE_URL . $endpoint . '/?'. $param_hidden .'='. $value->$param_hidden;
            $set_html_value = '<a href="' . $url .'">'.$set_html_value .'</a>';
        }

        $html .= <<<HTML
            <td>{$set_html_value}</td>
        HTML;
        $cont++;

    }

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