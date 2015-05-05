<?php
namespace MyFrameWork\DataType;

use MyFrameWork\DataType\DatatypeString;

class DatatypeColor extends DatatypeString {
    
    protected function getCharsAllowed() {
        return 7;
    }
    
    protected function getHTMLInputType() {
        return 'color';
    }
    
}
