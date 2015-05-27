<?php
namespace MyFrameWork\DataType;

use MyFrameWork\DataType\Datatype;
use MyFrameWork\Memory\MemoryPage;
use MyFrameWork\HTML;

/**
 * Represent an Html Editor component
 */
class DatatypeHtml extends Datatype {
    
    /**
     * Retorna o html que monta um editor de html
     * 
     * @param  string $name   O nome do component
     * @param  string $value  O valor que o componente exibe para o usuário
     * @param  string $params Os parâmetros do compomente passados pela classe Flag  
     * @param  string $attr   Atributos do html do componente
     * @return string         Retorna todo o html formatado e pronto para ser renderizado em qualquer view
     */
    public function getHTMLEditable($name, $value, $params, $attr=array()) {
        MemoryPage::addCss('static/plugin/bootstrap-wysihtml5/bootstrap-wysihtml5-0.0.2.css');
        MemoryPage::addJs('static/plugin/bootstrap-wysihtml5/wysihtml5-0.3.0.min.js');
        MemoryPage::addJs('static/plugin/bootstrap-wysihtml5/bootstrap-wysihtml5-0.0.2.js');
        MemoryPage::addJs('static/plugin/bootstrap-wysihtml5/bootstrap-wysihtml5.pt-BR.js');        
        MemoryPage::addJs('static/plugin/bootstrap-wysihtml5/lib/js/bootstrap-button.js');
        MemoryPage::addJs('static/plugin/bootstrap-wysihtml5/htmleditor.js');
        
        $params = $this->normalizeParams($params);
        if (empty($attr['class'])) {
            $attr['class'] = 'htmleditor';
        }
        else {
            $classes = explode(' ', $attr['class']);
            if (!in_array('htmleditor', $classes)) {
                $attr['class'] .= ' htmleditor';
            }
        }
        $attr['rows'] = 10;
        $attr = $this->getHTMLAttributes($attr, $params);
        
        
        #. '<small>Para vídeos use: [vimeo:Id_do_video] (ex: [vimeo:115835208]), altura e largura ([vimeo:123123 width=600 height=300])</small>'
        return HTML::textarea($name, $attr, $name . '_id', $value);
    }
    
    protected function _toHumanFormat($value, $params) {
        $parts = explode('[vimeo:', $value);
        $t = count($parts);
        for ($i = 1; $i < $t; $i++) {
            $posidvideo = strpos($parts[$i], ']');
            if ($posidvideo) {
                $idvideo = substr($parts[$i], 0, $posidvideo);
                $parts[$i] = substr($parts[$i], $posidvideo + 1);
            }
            else {
                $idvideo = $parts[$i];
                $parts[$i] = '';
            }
            $idvideo = trim(strip_tags($idvideo));
            $parts[$i] = '<iframe src=//player.vimeo.com/video/' . $idvideo . ' frameborder="0"></iframe>' . $parts[$i] ; 
        }
        return implode(' ', $parts);
    }
    
}
