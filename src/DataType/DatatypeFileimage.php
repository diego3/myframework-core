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
            'data-imgid'     => $name . '_img_id',
            'class' => 'filemanager-action-link'
        ];
        
        $linkextra = array_merge($linkextra, $attr);
        
        $imgattr = [
            'class' => 'imgfile img-responsive',
            'id'    => $name . '_img_id'
        ];
        
        $hasOrdenator = getValueFromArray($params, Flag::FILEIMAGE_HAS_ORDENATOR, false);
        
        $placeholder = '<small>' . getValueFromArray($params, Flag::PLACEHOLHER, '') . '</small>';
        
        if (empty($value)) {
            $helpText = getValueFromArray($params, Flag::FILEIMAGE_HELP_TEXT, false);
            if(!$helpText) {
                $helpText = 'Adicionar imagem';
            }
            
            $noimg = "";
            $showImgComponent = getValueFromArray($params, Flag::FILEIMAGE_SHOW_IMGCOMPONENT, true);
            if($showImgComponent){
                $noimg = HTML::img('image/icons/img-icon.png', 'Nenhuma imagem selecionada', $imgattr);
                $img   = $noimg . HTML::link($link, $helpText, 'Adicionar imagem', $linkextra);
            }
            else {
               $img   = HTML::link($link, $noimg . $helpText, 'Adicionar imagem', $linkextra); 
            }
            
            if($hasOrdenator) {
                $ordem = $linkextra["data-ordem"];
                $paginaformandoid = isset($linkextra["data-pagina_id"]) ? $linkextra["data-pagina_id"] : '';
                $img .= "<div class='fileimage-ordem' title='ordem da imagem nesta página personalizada'>{$ordem}</div>";
                $img .= "<div class='glyphicon glyphicon-trash unselect' 
                        data-ordem='".$ordem."' data-paginaformando_id=' ".$paginaformandoid." '   style='display:none;'
                        title='clique aqui para remover esta imagem!'></div>";
            }
        }
        else {
            $img  = HTML::img($value, 'Imagem selecionada', $imgattr);
            $img .= HTML::link($link, 'Alterar imagem', 'Trocar a imagem', $linkextra);
            if($hasOrdenator) {
                $ordem = $linkextra["data-ordem"];
                $paginaformandoid = isset($linkextra["data-pagina_id"]) ? $linkextra["data-pagina_id"] : '';
                $img .= "<div class='fileimage-ordem' title='ordem da imagem nesta página personalizada'>{$ordem}</div>";
                $img .= "<div class='glyphicon glyphicon-trash unselect' 
                        data-ordem='".$ordem."' data-paginaformando_id=' ".$paginaformandoid." '   
                        title='clique aqui para remover esta imagem!'></div>";
            }
        }
        return $placeholder . $img . HTML::input($name, array('value' => $value), $name . '_id', 'hidden');
    }
}

