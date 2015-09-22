<?php

namespace MyFrameWork\DataType;

use MyFrameWork\DataType\DatatypeStringBase;
use MyFrameWork\Enum\Flag;
use MyFrameWork\Factory;

/* 
 * Define o tipo de dado email
 */
class DatatypeEmail extends DatatypeStringBase {    
    protected function _sanitize($value, $params = null) {
        if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return filter_var($value, FILTER_SANITIZE_EMAIL);
        }
        return null;
    }
    
    protected function _isValid($value, $params) {
        $result = isValidEmail($value, getValueFromArray($params, Flag::VALIDATE_DOMAIN, false));
        if(!$result) {
            Factory::log()->warn("O e-mail {$value} não é um e-mail válido");
            return false;
        }
        return true;
    }
    
    public function isEmpty($value) {
        return empty($value);
    }
}

