<?php
namespace MyFrameWork\DataType;

use MyFrameWork\DataType\Datatype;
use MyFrameWork\Enum\Flag;
use MyFrameWork\Memory\MemoryPage;
use MyFrameWork\HTML;

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
    
    /**
     * Retorna o html para renderizar o elemento na página
     * 
     * @param  string $name   O nome do componente
     * @param  string $value  O caminho da imagem
     * @param  array  $params Parâmetros utilizados com as Flag::CONSTANTES
     * @param  array  $attr   Atributos html para o elemento
     * @return string         Retorna o html para o elemento
     */
    public function getHTMLEditable($name, $value, $params, $attr = array()) {
        MemoryPage::addCss('static/css/page/filemanager.css');
        MemoryPage::addJs("js/modal-fileupload.js");
        MemoryPage::addJs("static/plugin/bootstrap-fileinput-master/js/fileinput.min.js");
        MemoryPage::addCss('static/plugin/bootstrap-fileinput-master/css/fileinput.min.css');
        
        $params = $this->normalizeParams($params);
        $link = 'filemanager/index?path=' . getValueFromArray($params, Flag::MOVE_TO, 'image/') . '&header=false';
        $linkextra = [
            'data-toggle'    => 'modal',
            'data-target'    => '#myFileUpload',
            'data-up-action' => 'fileupload',
            'data-hiddenid'  => $name . '_id',
            'data-imgid'     => $name . '_img_id'
        ];
        
        $linkextra = array_merge($linkextra, $attr);
        
        $imgattr = [
            'class' => 'imgfile img-responsive',
            'id'    => $name . '_img_id'
        ];
        
        $hasOrdenator = getValueFromArray($params, Flag::FILEIMAGE_HAS_ORDENATOR, false);
        
        $msg = '<small>' . getValueFromArray($params, Flag::PLACEHOLHER, '') . '</small>';
        
        if (empty($value)) {
            $noimg = HTML::img('image/icons/img-icon.png', 'Nenhuma imagem selecionada', $imgattr);
            $img   = HTML::link($link, $noimg . 'Adicionar imagem', 'Adicionar imagem', $linkextra);
            if($hasOrdenator) {
                $ordem = $linkextra["data-ordem"];
                $img .= "<div class='fileimage-ordem' title='ordem da imagem nesta página personalizada'>{$ordem}</div>";
            }
        }
        else {
            $img  = HTML::img($value, 'Imagem selecionada', $imgattr);
            $img .= HTML::link($link, 'Alterar imagem', 'Trocar a imagem', $linkextra);
            if($hasOrdenator) {
                $ordem = $linkextra["data-ordem"];
                $img .= "<div class='fileimage-ordem' title='ordem da imagem nesta página personalizada'>{$ordem}</div>";
            }
        }
        return $msg . $img . HTML::input($name, array('value' => $value), $name . '_id', 'hidden');
    }
}

