<?php
namespace MyFrameWork\DataType;

use MyFrameWork\DataType\Datatype;

/* 
 * Define o tipo de dado primitivo lógico
 * TRUE values is "1", "true", "on" and "yes"
 * FALSE values is "0", "false", "off", "no" and ""
 */
class DatatypeBoolean extends Datatype {   
    /**
     * Processa o valor retornando-o no tipo base
     * @param mixed $value
     * @return float
     */
    public function valueOf($value) {
        if (is_null($value)) {
            return null;
        }
        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }
    
    protected function _isValid($value, $params=array()) {
        return is_bool($this->valueOf($value));
    }
    
    protected function _toHumanFormat($value, $params) {
        $value = $this->valueOf($value);
        if (is_bool($value)) {
            if ($value) {
                return getValueFromArray($params, Flag::TRUE_LABEL, 'verdadeiro');
            }
            else {
                return getValueFromArray($params, Flag::FALSE_LABEL, 'falso');
            }
        }
        return '';
    }
    
    public function getHTMLEditable($name, $value, $params, $attr=array()) {
        $params = $this->normalizeParams($params);
        if (!$this->_isValid($value)) {
            $value = getValueFromArray($params, Flag::DEFAULT_VALUE);
        }
        $attr = $this->getHTMLAttributes($attr, $params);
        $options = array(
            'true' => getValueFromArray($params, Flag::TRUE_LABEL, 'Verdadeiro'),
            'false' => getValueFromArray($params, Flag::FALSE_LABEL, 'Falso')
        );
        if (!getValueFromArray($params, Flag::REQUIRED, false)) {
            array_unshift($options, '');
        }
        return HTML::select($name, $options, var_export($value, true), $attr, $name . '_id');
    }
}

