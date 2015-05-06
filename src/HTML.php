<?php

namespace MyFrameWork;
use MyFrameWork\Template;

/**
 * Classe utilitária para geração de conteúdo HTML
 */
class HTML {
    private static $completeElement = "<{{element}}{{#attrs}} {{name}}='{{value}}'{{/attrs}}>{{#closetag}}{{{content}}}</{{element}}>{{/closetag}}";
    
    /**
     * Renderiza um elemento HTML
     * @param string $element Nome do elemento
     * @param string $content Conteúdo interno do elemento. Se este valor for NULO então não haverá tag de fechamento
     * @param array $attrs Array de atributos do elemento (nomedoatributo => valordoatributo)
     * @return string
     */
    protected static function renderElement($element, $content, $attrs) {
        $r = array();
        foreach ($attrs as $key => $value) {
            $r[] = array('name' => $key, 'value' => $value);
        }
        $params = array('element' => $element, 'content' => $content, 'attrs' =>  $r, 'closetag' => !is_null($content));
        return Template::singleton()->renderHTML(self::$completeElement, $params);
    }
    
    /**
     * Retorna um link em HTML
     * @param string $url URL que o link aponta
     * @param string $label Conteúdo do link
     * @param string $title Texto alternativo para o link
     * @param array $extra Atributos extras para o elemento
     * @return string
     */
    public static function link($url, $label, $title, $extra=array()) {
        $extra['href'] = $url;
        $extra['title'] = $title;
        return self::renderElement('a', $label, $extra);
    }
    
    /**
     * Retorna um imagem em HTML
     * @param string $src URL para a imagem
     * @param string $alt Texto alternativo para a imagem
     * @param array $extra Atributos extras para o elemento
     * @return string
     */
    public static function img($src, $alt, $extra=array()) {
        $extra['src'] = $src;
        $extra['alt'] = $alt;
        return self::renderElement('img', null, $extra);
    }
    
    public static function input($name, $attrs=array(), $id=null, $type='text') {
        $attrs['type'] = $type;
        $attrs['name'] = $name;
        if (empty($id)) {
            $id = $name;
        }
        $attrs['id'] = $id;
        return self::renderElement('input', null, $attrs);
    }
    
    /**
     * Renderiza um elemento Html do tipo select. <select><option value=''>realvalue</option></select>
     * @param string $name
     * @param array $options
     * @param string $selected
     * @param array $attrs
     * @param int $id
     * @return string
     */
    public static function select($name, $options, $selected=null, $attrs=array(), $id=null) {
        $attrs['name'] = $name;
        if (empty($id)) {
            $id = $name;
        }
        $attrs['id'] = $id;
        $content = array();
        foreach ($options as $k => $v) {
            $opt = array('value' => $k);
            if ($k == $selected) {
                $opt['selected'] = 'selected';
            }
            $content[] = self::renderElement('option', $v, $opt);
        }
        return self::renderElement('select', implode("\n", $content), $attrs);
    }
    
    public static function textarea($name, $attrs=array(), $id=null, $value='') {
        $attrs['name'] = $name;
        if (empty($id)) {
            $id = $name;
        }
        $attrs['id'] = $id;
        if (empty($attrs['rows'])) {
            $attrs['rows'] = 5;
        }
        return self::renderElement('textarea', $value, $attrs);
    }
    
    public static function table($columns, $data, $class='table', $caption='') {
        $table = '<table class="' . $class . '">';
        if (!empty($caption)) {
            $table .= '<caption>' . $caption . '</caption>';
        }
        $table .= '<thead><tr>';
        foreach ($columns as $th) {
            $table .= '<th>' . $th . '</th>';
        }
        $table .= '</tr></thead><tbody>';
        foreach ($data as $row) {
            $table .= '<tr>';
            foreach ($row as $td) {
                $table .= '<td>' . $td . '</td>';
            }
            $table .= '</tr>';
        }
        $table .= '</tbody></table>';
        return $table;
    }
}
