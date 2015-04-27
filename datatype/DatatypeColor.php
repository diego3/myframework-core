<?php
require_once PATH_MYFRAME . '/datatype/DatatypeString.php';

class DatatypeColor extends DatatypeString {
    
    protected function getCharsAllowed() {
        return 7;
    }
    
    protected function getHTMLInputType() {
        return 'color';
    }
    
}
