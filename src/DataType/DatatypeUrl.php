<?php
namespace MyFrameWork\DataType;

use MyFrameWork\DataType\Datatype;
use MyFrameWork\HTML;
use MyFrameWork\Enum\Flag;

class DatatypeUrl extends Datatype {
    
    # TODO make validations for especific URL format ???
    
    public function getHTMLEditable($name, $value, $params, $attr = array()) {
        
        
        return HTML::link(getValueFromArray($params, Flag::URL), getValueFromArray($params, Flag::LABEL), "", $attr);
    }
    
}
