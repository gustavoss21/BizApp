<?php

if(!isset($allowedRoute)){
    die('<div style="color:red;">Rota não encontrada</div>');
}

$html = <<<HTML
    <h1 style="text-align: center;color:rgb(148 11 11)">NOTIFICAÇÃO</h1>
    <div class="content-form" id="content-notification">
        <p> O cliente <strong>{$data->$item_name} sera removido</strong>, deseja continuar?</p>
        <form method="post" action="{$submit_link}">
            <input name="{$parameter_id}" value="{$data->$parameter_id}" hidden>
            <button  class="input-submit input-element" type="submit">remover</button> 
        </form>
        <a href="{$link_base}" style="display: inline-block; margin-top: 10px;">
               voltar
        </a>
    </div>
HTML;

return $html;