<?php

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

class MemoryPage extends Memory {
    
    /**
     * Define um titulo para a página sobrescrevendo o titulo gerado pelo ProcessRequest
     * @param string $title
     */
    public static function setTitle($title) {
        self::set('_title', $title);
    }
    
    /**
     * Retorna o título da página
     * @param string $default Título utilizado se nenhum valor for retornado
     * @return string
     */
    public static function getTitle($default='') {
        return self::get('_title', $default);
    }
    
    /**
     * Adiciona um arquivo javascript que deverá ser adicionado a resposta
     * @param string $jsfile Path para um arquivo javascript
     */
    public static function addJs($jsfile) {
        self::add('_js', $jsfile, true);
    }
    
    /**
     * Adiciona um arquivo Css que deverá ser adicionado a resposta
     * @param string $cssfile path para um arquivo css
     */
    public static function addCss($cssfile) {
        self::add('_css', $cssfile, true);
    }
    
    /**
     * Retorna a lista de arquivos Javascript
     * @return array
     */
    public static function getJs() {
        return self::get('_js', array());
    }
    
    /**
     * Retorna a lista de arquivos CSS
     * @return array
     */
    public static function getCss() {
        return self::get('_css', array());
    }
    
    /**
     * Adiciona conteúdo extra ao header da página
     * @param string $content
     */
    public static function setExtraHeader($content) {
        self::set('_extraheader', $content);
    }
    
    /**
     * Retorna o código Extra que devará ser adicionada ao header das páginas
     */
    public static function getExtraHeader() {
        return self::get('_extraheader', '');
    }
    
    /**
     * Define um atributo padrão que será enviado para o mustache
     * @param string $name
     * @param mixed $value
     */
    public static function setAttribute($name, $value) {
        $attributes = self::get('_attributes', array());
        $attributes[$name] = $value;
        self::set('_attributes', $attributes);
    }
    
    /**
     * Retorna o valor de um atributo da página
     * @param type $name
     */
    public static function getAttribute($name, $default=null) {
        $attributes = self::get('_attributes', array());
        return getValueFromArray($attributes, $name, $default);
    }
    
    /**
     * Retorna os atributos padrões que serão enviados para o mustache
     * @return array
     */
    public static function getAttributes() {
        return self::get('_attributes', array());
    }
}
