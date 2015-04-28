<?php
require_once PATH_MYFRAME . '/datatype/Datatype.php';

/* 
 * Define o tipo de dado file
 * Deve apontar sempre para um arquivo válido no sistema
 */
class DatatypeFileimage extends Datatype {   
    /**
     * Processa o valor retornando-o no tipo base
     * @param mixed $value
     * @return string
     */
    public function valueOf($value) {
        return $value;
    }
    
    protected function _isValid($value, $params) {
        return file_exists(PATH_APP . '/static/' . $value);
    }
    
    public function isEmpty($value) {
        return empty($value);
    }
    
    public function getHTMLEditable($name, $value, $params, $attr=array()) {
        MemoryPage::addJs("js/modal-fileupload.js");
        MemoryPage::addJs("static/plugin/bootstrap-fileinput-master/js/fileinput.min.js");
        MemoryPage::addCss('static/plugin/bootstrap-fileinput-master/css/fileinput.min.css');
        
        $params = $this->normalizeParams($params);
        $link = 'filemanager/?path=' . getValueFromArray($params, Flag::MOVE_TO, 'image/empresa') . '&header=false';
        $linkextra = array(
            'data-toggle' => 'modal',
            'data-target' =>  '#myFileUpload',
            'data-up-action' => 'fileupload',
            'data-hiddenid' => $name . '_id',
            'data-imgid' => $name . '_img_id'
        );
        $imgattr = array(
            'class' => 'imgfile img-responsive',
            'id' => $name . '_img_id'
        );
        $msg = '<small>' . getValueFromArray($params, Flag::PLACEHOLHER, '') . '</small>';
        if (empty($value)) {
            $noimg = HTML::img(
                'image/icons/img-icon.png', 'Nenhuma imagem selecionada', $imgattr);
            $img = HTML::link($link, $noimg . 'Adicionar imagem', 'Adicionar imagem', $linkextra);
        }
        else {
            $img = HTML::img($value, 'Imagem selecionada', $imgattr);
            $img .= HTML::link($link, 'Alterar imagem', 'Trocar a imagem', $linkextra);
        }
        return $msg . $img . HTML::input($name, array('value' => $value), $name . '_id', 'hidden');
    }
}

