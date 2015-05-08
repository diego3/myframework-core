<?php
namespace MyFrameWork\DataType;

use MyFrameWork\DataType\Datatype;
use MyFrameWork\Enum\Flag;
use MyFrameWork\HTML;

/* 
 * Define o tipo de dado Data
 */
class DatatypeDate extends Datatype {
    
    /**
     * Retorna o formato da data
     * Se nenhhum formato for informado o padrão será Flag::DATE_FORMAT_BRAZIL
     * @param array $params
     * @return string
     */
    protected function getDateFormat($params) {
        if (getValueFromArray($params, Flag::DATE_FORMAT_BRAZIL, false)) {
            return Flag::DATE_FORMAT_BRAZIL;
        }
        else if (getValueFromArray($params, Flag::DATE_FORMAT_USA, false)) {
            return Flag::DATE_FORMAT_USA;
        }
        return Flag::DATE_FORMAT_ISO;
    }
    
    /**
     * Cria um objeto data
     * @param string $value Data
     * @return string no formato da data
     */
    public function valueOf($value) {
        $parts = explode('-', str_replace(array('/', '.'), '-', $value));
        if (count($parts) == 3) {
            foreach ($parts as $part) {
                if (!is_numeric($part)) {
                    return null;
                }
            }
            return $parts[0] . '-' . $parts[1] . '-' . $parts[2]; 
        }
        return null;
    }
    
    /**
     * Retorna a data no formato DateTime
     * @param string $value
     * @param array $params
     * @return DateTime
     */
    protected function _sanitize($value, $params) {
        if (!$this->isEmpty($value)) {
            $format = $this->getDateFormat($params);
            $date = array_combine(explode('-', $format), explode('-', $value));
            if (checkdate($date['m'], $date['d'], $date['Y'])) {
                return \DateTime::createFromFormat('Y-m-d', $date['Y'] . '-' . $date['m'] . '-' . $date['d']);
            }
        }
        return null;
    }
    
    protected function _isValid($value, $params) {
        if ($this->isEmpty($value)) {
            return false;
        }
        else if ($value instanceof \DateTime) {
            return true;
        }
        $format = explode('-', $this->getDateFormat($params));
        $parts = explode('-', $value);
        if (count($format) != count($parts)) {
            return false;
        }
        $date = array_combine($format, $parts);
        return checkdate($date['m'], $date['d'], $date['Y']);
    }
    
    public function isEmpty($value) {
        return empty($value);
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
        return HTML::input($name, $attr, $name . "_id", "date");
    }
}

