<?php

namespace MyFrameWork\Enum;
use ReflectionClass;
/* 
 * Classe base para todos os enums utilizados no sistema
 */

abstract class BasicEnum {
    private static $constCache = array();

    /**
     * Retorna uma lista mapeando os nomes com os valores
     * @return array
     */
    private static function getConstants() {
        $classname = get_called_class();
        if (empty(self::$constCache[$classname])) {
            $reflect = new ReflectionClass(get_called_class());
            self::$constCache[$classname] = $reflect->getConstants();
        }

        return self::$constCache[$classname];
    }

    /**
     * Define se um nome é válido para o ENUM
     * @param string $name O nome do tipo
     * @param boolean $strict Maiusculo e Minusculo fazem a diferença
     * @return boolean
     */
    public static function isValidName($name, $strict = false) {
        $constants = self::getKeys();

        if ($strict) {
            return in_array($name, $constants);
        }

        return in_array(strtolower($name), array_map('strtolower', $constants));
    }

    /**
     * Verifica se o valor é válido para o respectivo Enum
     * @param mixed $value O valor que será verificado
     * @param boolean $strict Maiusculo e Minusculo fazem a diferença
     * @return boolean
     */
    public static function isValidValue($value, $strict = true) {
        $values = self::getValues();
        return in_array($value, $values, $strict);
    }
    
    /**
     * O mesmo que is validName
     * @see self::isValidValue($value)
     */
    public static function isValid($value) {        
        return self::isValidValue($value);
    }
    
    /**
     * Retorna todas as chaves
     * @return array
     */
    public static function getKeys() {
        return array_keys(self::getConstants());
    }
    
    /**
     * Retorna todos os valores
     * @return array
     */
    public static function getValues() {
        return array_values(self::getConstants());
    }
    
    /**
     * Retorna o valor do enum conforme o número ou null se não for encontrado
     * @param string $name O nome do tipo
     * @param boolean $strict Maiusculo e Minusculo fazem a diferença
     * @return mixed
     */
    public static function getValueByName($name, $strict=false) {
        if (self::isValidName($name, $strict)) {
            $values = self::getConstants();
            $key = strtolower($name);
            $orignkeys = array_keys($values);
            $keys = array_map('strtolower', $orignkeys);
            foreach ($keys as $i => $v) {
                if ($v == $key) {
                    $name = $orignkeys[$i]; 
                    break;
                }
            }
            return $values[$name];
        }
        return null;
    }
    
    abstract public function labels();
    
    public function mustache() {
        $data = array();
        foreach ($this->labels() as $k => $v) {
            $data[] = array('key' => $k, 'value' => $v);
        }
        return $data;
    }
    
    public function getLabel($value, $default=null) {
        return getValueFromArray($this->labels(), $value, $default);
    }
}