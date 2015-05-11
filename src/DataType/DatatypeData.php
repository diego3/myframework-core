<?php

namespace MyFrameWork\DataType;

use MyFrameWork\DataType\DatatypeDate;

/**
 * Alias for DatatypeDate in PT_BR
 */
class DatatypeData extends DatatypeDate {
    
    /**
     * Retorna a data no formato string 
     * @param string $value
     * @param array $params
     * @return string
     */
    protected function _sanitize($value, $params) {
        if (!$this->isEmpty($value)) {
            $format = $this->getDateFormat($params);
            $date = array_combine(explode('-', $format), explode('-', $value));
            if (checkdate($date['m'], $date['d'], $date['Y'])) {
                $date =  \DateTime::createFromFormat('Y-m-d', $date['Y'] . '-' . $date['m'] . '-' . $date['d']);
                return $date->format($format);
            }
        }
        return null;
    }
}
