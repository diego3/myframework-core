<?php

namespace MyFrameWork\Memory;

use MyFrameWork\Memory\Memory;

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