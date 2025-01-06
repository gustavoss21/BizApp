<?php


namespace Api\controller;

use DateTime;

class Controller{
    protected static function setClassParameters(object &$class, array $parameters)
    {
        foreach ($parameters as $parameter => $value) {
            $class->setParameter($parameter, $value);
    
        }
    }

    public function projectionData($days){
        $interval = "+$days days";
        $date = new DateTime();
        $date->modify($interval);
        return $date->format('d/m/Y H:i:s');
    }
}