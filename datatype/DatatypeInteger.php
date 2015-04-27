<?php
require_once PATH_MYFRAME . '/datatype/DatatypeNumeric.php';

/**
 * Representa o tipo de dado inteiro
 */
class DatatypeInteger extends DatatypeNumeric {
    
    protected function _isValid($value, $params) {
        return is_int(filter_var($value, FILTER_VALIDATE_INT));
    }
    
    /**
     * Processa o valor retornando-o no tipo base
     * @param mixed $value
     * @return int
     */
    public function valueOf($value) {
        if (!is_numeric($value)) {
            $value = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
        }
        if (is_numeric($value)) {
            return intval($value);
        }
        return null;
    }
    
}
