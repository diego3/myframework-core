<?php
require_once PATH_MYFRAME . '/datatype/DatatypeText.php';

/**
 * O tipo de dado string não permite valores com marcação HTML
 * E não considera espaços no início e no final do texto
 * e possui um tamanho limitado de caracteres
 */
class DatatypeString extends DatatypeText {
    const MAX_CHARS_ALLOWED = 256;
    
    protected function normalizeParams($params) {
        $max = getValueFromArray($params, Flag::MAX_VALUE_INCLUSIVE, $this->getCharsAllowed());
        if ($max >= $this->getCharsAllowed()) {
            $params[Flag::MAX_VALUE_INCLUSIVE] = $this->getCharsAllowed();
        }
        return parent::normalizeParams($params);
    }
    
    protected function getCharsAllowed() {
        return self::MAX_CHARS_ALLOWED;
    }
    
    protected function getHTMLInputType() {
        return 'text';
    }
    
    public function getHTMLEditable($name, $value, $params, $attr=array()) {
        $params = $this->normalizeParams($params);
        if (empty($value)) {
            $attr['value'] = getValueFromArray($params, Flag::DEFAULT_VALUE, '');
        }
        else {
            $attr['value'] = $value;
        }
        $attr = $this->getHTMLAttributes($attr, $params);
        return HTML::input($name, $attr, $name . '_id', $this->getHTMLInputType());
    }
}
