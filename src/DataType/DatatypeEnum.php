<?php

namespace MyFrameWork\DataType;

use MyFrameWork\DataType\Datatype;

/* 
 * Define o tipo de dado Enum
 */
class DatatypeEnum extends Datatype {
    
    /**
     * Retorna o enum passado em $params
     * @param array $params
     * @return BasicEnum
     */
    private function getEnum($params) {
        $enumname = getValueFromArray($params, Flag::ENUM_NAME, '');
        if (empty($enumname)) {
            Factory::log()->fatal('É necessário informar o nome do tipo enum');
            require_once PATH_MYFRAME . '/enum/EmptyEnum.php';
            return new EmptyEnum();
        }
        return Factory::enum($enumname);
    }
    
    protected function _sanitize($value, $params) {
        $value = filter_var($value, FILTER_SANITIZE_STRING);
        if ($this->_isValid($value, $params)) {
            return $value;
        }
        return null;
    }
    
    protected function _toHumanFormat($value, $params) {
        $enum = $this->getEnum($params);
        return $enum->getLabel($value, '');
    }
    
    protected function _isValid($value, $params) {
        $enum = $this->getEnum($params);
        return $enum->isValid($value);
    }
    
    public function isEmpty($value) {
        return empty($value);
    }
    
    public function getHTMLEditable($name, $value, $params, $attr=array()) {
        $params = $this->normalizeParams($params);
        $attr = $this->getHTMLAttributes($attr, $params);
        $enum = $this->getEnum($params);
        $options = $enum->labels();
        if (!getValueFromArray($params, Flag::REQUIRED, false)) {
            array_unshift($options, '');
        }
        return HTML::select($name, $options, $value, $attr, $name . '_id');
    }
}

