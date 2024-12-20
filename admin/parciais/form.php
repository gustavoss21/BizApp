<?php

if (!isset($allowedRoute)) {
    die('<div style="color:red;">Rota não encontrada</div>');
}

class SetForm
{
    public static $optionPosition = '';
    public $titleForm;
    public $attributes;
    public $message;
    public $openForm;
    public $elements;
    public $closeForm;
    public $input_errors;

    public function __construct(string $titleForm, $attributes,$input_errors)
    {
        // class="from-control" id="$idForm" action="$this->actionForm" method="$this->methodForm"
        $this->titleForm = $titleForm;
        $this->attributes = $attributes;
        $this->input_errors = $input_errors;
        $this->message = '';
        $this->openForm = '';
        $this->elements = [];
        $this->closeForm = '';
    }

    public function setMessage(string $message = '')
    {
        $this->message = $message;
    }

    public function setElement(string $tag, array $parameters = [], $innerElement = '', array $nonKayAttributes = [],int $position = null)
    {
        $this->elements[] = ['tag' => $tag, 'attributes' => $parameters, 'nonKayAttributes' => $nonKayAttributes, 'innerElement' => $innerElement];
    }

    public function setInputError(array $input_error = [])
    {
    }

    public function buildElement(string $tag, array $attributes = [], $innerElement = '', array $nonKayAttributes = []): string
    {
        $elements = '';

        //set parameters
        foreach ($attributes as $kay => $attribute) {
            $elements .= "$kay='$attribute' ";
        }

        foreach ($nonKayAttributes as $kay => $attribute) {
            $elements .= "$attribute";
        }
        /////////
        if ($tag === 'input') {
            $activeClassError = 'error-active';
            $inactiveClassError = 'error-inactive';
            $tagError = "<p class='$inactiveClassError'></p>";
            $groupInput = "<label for='{$attributes['name']}'>$innerElement</label><input $elements>";
            $elementError = $this->input_errors[$attributes['name']]??null;
            
            if($elementError){
                $tagError = "<p class='$activeClassError'>$elementError</p>";
            }

            $groupInput .= $tagError;
            
            return $groupInput;
        }

        return "<$tag $elements>$innerElement</$tag>";
    }

    protected function buildinnerForm()
    {
        $innerForm = '';

        foreach ($this->elements as $field) {
            $innerForm .= '<div class="content-input">'.$this->buildElement(...$field).'</div>';
        }

        return $innerForm;
    }

    public function buildForm()
    {
        $innerForm = $this->buildinnerForm();
        $form = <<<HTML
            <h1 style="text-align: center;color:rgb(148 11 11)">$this->titleForm</h1>
            <p id="message"></p>
            <div class="content-form">
                <form $this->attributes >$innerForm</form>
            </div>
        HTML;

        return $form;
    }
}


