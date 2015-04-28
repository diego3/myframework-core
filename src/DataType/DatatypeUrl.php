<?php

require_once PATH_MYFRAME . '/datatype/Datatype.php';

class DatatypeUrl extends Datatype {
    
    
    public function getHTMLEditable($name, $value, $params, $attr = array()) {
        
        
        return HTML::link(getValueFromArray($params, Flag::URL), getValueFromArray($params, Flag::LABEL), "", $attr);
    }
    
}
