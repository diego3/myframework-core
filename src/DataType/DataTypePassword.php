<?php

namespace MyFrameWork\DataType;

use MyFrameWork\DataType\DatatypeString;

/**
 * Description of DataTypePassword
 *
 * @author Diego
 */
class DataTypePassword  extends DatatypeString {
    
    protected function getHTMLInputType() {
        return "password";
    }
    
    /**
     * Processa o valor retornando-o no tipo base
     * @param mixed $value
     * @return mixed
     */
    public function valueOf($value) {
        return $value;
    }
}
