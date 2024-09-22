<?php

$html = <<<HTML

    <h1 style="text-align: center;color:rgb(148 11 11)">$subtitle</h1>
    <div class="content-form">
        <form class="from-control" action="{$data['uri']}" method="post">
            
    HTML;

// main body / set form

//set input form
foreach ($data['inputs'] as $input_data) {
    $html .= <<<HTML
            <div class="content-input">
                <label for="{$input_data['identifier']}">{$input_data['label']}</label>
                <input class="input-element" type="{$input_data['type']}" name="{$input_data['identifier']}" id="{$input_data['identifier']}" value="{$input_data['value']}" >
            HTML;
    // set input error message
    if ($input_data['text_error']) {
        $html .= <<<HTML
            <p style="color:red;">{$input_data['text_error']}</p>
        HTML;
    }
    $html .= '</div>';
}

//set button form
foreach ($data['elements'] as $element) {
    $html .= <<<HTML
        <{$element['tag_type']} {$element['action']} id="{$element['identifier']}" class="{$element['class']}">{$element['label']}</{$element['tag_type']}>
        HTML;
}

// close tags
$html .= <<<HTML
        </form>
    </div>
HTML;

return $html;
