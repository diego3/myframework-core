<?php
namespace MyFrameWork\DataType;

use MyFrameWork\DataType\DatatypeStringBase;
use MyFrameWork\Enum\Flag;

/**
 * O tipo de dado texto não considera espaços no início e no final do texto
 */
class DatatypeText extends DatatypeStringBase {
   
    public function __construct() {
        $this->defaultParams[Flag::TRIM] = true;
    }
    
    protected function _sanitize($value, $params) {
        //If encoded tags is defined dont sanitize string
        if (getValueFromArray($params, Flag::ENCODE_TAGS, false)) {
            return $value;
        }
        return filter_var($this->valueOf($value), FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
    }
    
    /**
     * Define se uma string é vazia ou não
     * @param string $value
     * @return boolean
     */
    public function isEmpty($value) {
        return parent::isEmpty(trim($this->valueOf($value)));
    }
    
    public function getHTMLEditable($name, $value, $params, $attr=array()) {
        $params = $this->normalizeParams($params);        
        $attr = $this->getHTMLAttributes($attr, $params);   
        if (empty($value)) {
            $value = getValueFromArray($params, Flag::DEFAULT_VALUE, '');
        }
        return HTML::textarea($name, $attr, $name . '_id', $value);
    }
}
