<?php

namespace MyFrameWork\DataType;

use MyFrameWork\DataType\Datatype;
use Respect\Validation\Validator as v;

//require_once PATH_LOCAL . '/vendor/Validation/main.php';

/* 
 * Define o tipo de dado primitivo Numérico
 * Pode ser um valor real ou inteiro, todavia o formato interno será real
 */
class DatatypeNumeric extends Datatype {

    protected function _isValid($value , $params) {
        return is_numeric($value);
    }
    
    /**
     * Processa o valor retornando-o no tipo base
     * @param mixed $value
     * @return float
     */
    public function valueOf($value) {
        $value = filter_var(str_replace(',', '.', $value), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        if (is_numeric($value)) {
            return floatval($value);
        }
        return null;
    }
    
    public function isEmpty($value) {
        if (is_numeric($value)) {
            $value = floatval($value);
        }
        else if (is_string($value)) {
            $value = trim($value);
        }
        return empty($value);
    }
    
    /**
     * Define o tamanho máximo de casas decimais que o número deve ter
     * @param float $value
     * @param array $params Lista de flags
     */
    protected function decimalsize($value, $params) {
        $size = getValueFromArray($params, Flag::DECIMAL_SIZE);        
        if (getValueFromArray($params, Flag::TRUNCATE, false)) {
            $value = truncateDecimal($value, $size);
        }
        else {
            $value = round($value, $size);
        }
        return $value;
    }
    
    /**
     * Valida se o tamanho máximo de casas do número está dentro do esperado
     * @param float $value
     * @param array $params Lista de flags
     * @return boolean
     */
    public function validDecimalsize($value, $params) {
        if (!is_numeric($value)) {
            Factory::log()->info('validDecimalsize => Valor não numérico');
            return false;
        }
        $size = getValueFromArray($params, Flag::DECIMAL_SIZE);
        $value = strval($value);
        $tam = (strlen($value) - 1) - strpos($value, '.');
        if ($tam > $size) {
            Factory::log()->warn('O número não pode ter mais do que "' . $size . '" casas decimais');
            return false;
        }
        return true;
    }
    
    /**
     * Verifica se o valor é maior ou igual ao menor valor aceito
     * @param float $value
     * @param array $params Lista de flags
     * @return boolean
     */
    protected function validMinvalueinclusive($value, $params) {
        $minvalue = getValueFromArray($params, Flag::MIN_VALUE_INCLUSIVE);
        if (!is_null($minvalue) && !v::numeric()->min($minvalue, true)->validate($value)) {
            Factory::log()->warn('Valor deve ser maior ou igual a ' . $minvalue);
            return false;
        }
        return true;
    }
    
    /**
     * Verifica se o valor possui é menor ou igual ao maior valor aceito
     * @param float $value
     * @param array $params Lista de flags
     * @return boolean
     */
    protected function validMaxvalueinclusive($value, $params) {
        $maxvalue = getValueFromArray($params, Flag::MAX_VALUE_INCLUSIVE);
        if (!is_null($maxvalue) && !v::numeric()->max($maxvalue, true)->validate($value)) {
            Factory::log()->warn('Valor deve ser menor ou igual a ' . $maxvalue);
            return false;
        }
        return true;
    }
    
    /**
     * Garante que o número seja positivo
     * @param float $value
     * @return numeric
     */
    public function positive($value) {
        if ($value < 0) {
            return $value * -1;
        }
        return $value;
    }
    
    /**
     * Verifica se o valor é positivo
     * @param float $value
     * @return boolean
     */
    protected function validPositive($value) {        
        if (!v::numeric()->positive()->validate($value)) {
            Factory::log()->warn('Valor deve ser positivo');
            return false;
        }
        return true;
    }
    
    /**
     * Garante que o número seja negativo
     * @param float $value
     * @return numeric
     */
    public function negative($value) {
        if ($value > 0) {
            return $value * -1;
        }
        return $value;
    }
    
    /**
     * Verifica se o valor é negativo
     * @param float $value
     * @return boolean
     */
    protected function validNegative($value) {        
        if (!v::numeric()->negative()->validate($value)) {
            Factory::log()->warn('Valor deve ser negativo');
            return false;
        }
        return true;
    }
}

