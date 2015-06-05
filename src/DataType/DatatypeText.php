<?php
namespace MyFrameWork\DataType;

use MyFrameWork\DataType\DatatypeStringBase;
use MyFrameWork\Enum\Flag;
use MyFrameWork\HTML;
use MyFrameWork\Memory\MemoryPage;

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
        $attr   = $this->getHTMLAttributes($attr, $params); 
        
        if (empty($value)) {
            $value = getValueFromArray($params, Flag::DEFAULT_VALUE, '');
        }
        
        $maxlenght = getValueFromArray($params, Flag::MAXLENGHT, false);
        if( $maxlenght ) {
            MemoryPage::addJs("static/js/bootstrap-maxlength.min.js");
            MemoryPage::addJs("static/js/jquery.autosize.min.js");
            
            $extra = [
                "maxlength" => $maxlenght,
                "data-limite-caracteres" => $maxlenght
            ];
            
            $attr = array_merge($attr, $extra);
        }
        
        return HTML::textarea($name, $attr, $name . '_id', $value);
    }
    
    /**
     * 
     * @param string $name     The name of component
     * @param array  $configs  Settings 
     */
    protected function maxlengthConnect($name, array $configs = []) {
        $s = '<script>';
        $s .= '$("#' . $name . '_id").maxlength()';
        $s .= '</script>';
    }
}
