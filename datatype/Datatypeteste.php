<?php
require_once PATH_MYFRAME . '/datatype/Datatype.php';

class DatatypeHtml extends Datatype {
    
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
        return HTML::textarea($name, $attr, $name . '_id', $value);
    }
    
}
