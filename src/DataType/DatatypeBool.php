<?php

namespace MyFrameWork\DataType;

use MyFrameWork\DataType\DatatypeBoolean;

/**
 * Nome abreviado do tipo de dado boolean
 * Representa o tipo de dado lógico
 */
class DatatypeBool extends DatatypeBoolean {
    
    /**
     * Processa o valor retornando-o no tipo base
     * @param mixed $value
     * @return mixed float quando o valor não é nulo, caso seja nulo retorna 'false'
     */
    public function valueOf($value) {
        if (is_null($value)) {
            return "false";
        }
        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }
}
