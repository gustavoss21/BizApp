<?php

$html = <<<HTML

    <h1 style="text-align: center;color:rgb(148 11 11)">$subtitle</h1>
    <div class="content-form">
        <form class="from-control" action="{$data['uri']}" method="post">
            
    HTML;

// main body / set form

//set body form

foreach ($data['inputs'] as $input_data) {
    $html .= <<<HTML
        <div class="content-input">
            <label for="{$input_data['identifier']}">{$input_data['identifier']}</label>
            <input class="input-element" type="{$input_data['type']}" name="{$input_data['identifier']}" id="{$input_data['identifier']}" >
        </div>
        HTML;
}

foreach ($data['elements'] as $element) {
    $html .= <<<HTML
        <{$element['type']} {$element['action']} id="{$element['identifier']}" class="{$element['class']}">{$element['label']}</{$element['type']}>
        HTML;
}

// close tags
$html .= <<<HTML
        </form>
    </div>
HTML;

return $html;
