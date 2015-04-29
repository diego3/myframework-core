<?php

namespace MyFrameWork\Memory;

/* 
 * Classe utilizada para armazenar dados na memória durante uma requisição
 * Utilizado para troca de informações entre métodos, sem a necessidade de passagem de parâmetros
 * Possui métodos básicos como get e set para dados em gerais
 * Possui métodos especificos como dados de javascript e dados de CSS
 */
class Memory {
    /**
     * Contém os dados que armazenados
     * @var array
     */
    protected static $memory = array();
    
    
    /**
     * Define um valor na memória
     * @param string $key Chave utilizada para armazenar o valor
     * @param mixed $value Valor que deverá ser salvo
     */
    public static function set($key, $value) {
        self::$memory[$key] = $value;
    }
    
    /**
     * Adiciona um valor na memória
     * @param string $key Chave utilizada para armazenar o valor
     * @param mixed $value Valor que deverá ser adicionado
     * @param boolean $unique Default: false, só adiciona o valor caso ele não exista
     */
    public static function add($key, $value, $unique=false) {
        if (!isset(self::$memory[$key])) {
            self::$memory[$key] = array();
        }
        else if (!is_array(self::$memory[$key])) {
            self::$memory[$key] = array(self::$memory[$key]);
        }
        if (!$unique || !in_array($value, self::$memory[$key])) {
            self::$memory[$key][] = $value;
        }
    }
    
    /**
     * Retorna o(s) valor(es) da chave
     * @param string $key Chave para retornar o valor
     * @param mixed $default Valor a retornar caso a chave seja vazia, o valor padrão é null
     * @return mixed Retorna o valor armazenado na posição da chave($key).
     * Caso a chave procurada não exista, então retornará null ou então 
     * o valor configurado no segundo parâmetro desta função.
     */
    public static function get($key, $default=null) {
        if (isset(self::$memory[$key])) {
            return self::$memory[$key];
        }
        return $default;
    }
    
    /**
     * Exclui todos os valores de uma chave
     * @param type $key
     */
    public static function clear($key) {
        if (isset(self::$memory[$key])) {
            unset(self::$memory[$key]);
        }
    }
    
    /**
     * Limpa toda a memória
     */
    public static function clearAll() {
        self::$memory = array();
    }
}


