<?php

namespace MyFrameWork\DataType;

use MyFrameWork\DataType\DatatypeBoolean;
use MyFrameWork\Enum\Flag;
use MyFrameWork\HTML;
use MyFrameWork\Template;

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
    
    /**
     * 
     * @param array $attr
     * @param array $params
     * @return array
     */
    protected function getHTMLAttributes($attr, $params) {
        if (empty($attr['class'])) {
            $attr['class'] = '';
        }
        $attr['class'] .= '';
        
        if (getValueFromArray($params, Flag::REQUIRED, false)) {
            $attr['required'] = 'required';
        }
        return $attr;
    }
    
    public function getHTMLEditable($name, $value, $params, $attr=array()) {
        $params = $this->normalizeParams($params);
        if (!$this->_isValid($value)) {
            $value = getValueFromArray($params, Flag::DEFAULT_VALUE);
        }
        $attr = $this->getHTMLAttributes($attr, $params);
        
        if(is_bool($value) and $value) {
            $attr["checked"] = "checked";  
        } 
        
        $element  = "<div class='checkbox'>";
        $element .= "<label for='{$name}_id' >";
        $element .= HTML::input($name, $attr, $name . '_id', 'checkbox');
        $element .= getValueFromArray($params, Flag::LABEL) . "</label></div>";
        return $element;
    }
}
