<?php

namespace MyFrameWork\DataType;

use MyFrameWork\DataType\DataTypePK;
use MyFrameWork\Enum\Flag;
use MyFrameWork\HTML;
use MyFrameWork\Template;
use MyFrameWork\Memory\MemoryPage;
use MyFrameWork\Factory;

/**
 * Description of DataTypeMultiselect
 * 
 * Permite selecionar mais de um item
 * 
 * @author Diego Rosa dos Santos<diegosantos@alphaeditora.com.br>
 */
class DataTypeMultiselect extends DataTypePK {
    
    protected function _isValid($value, $params) {
        $dao = Factory::DAO(getValueFromArray($params, Flag::DAO_NAME, ''));
        if (!is_null($dao)) {
            return true;
        }
        return false;
    }
    
    /**
     * 
     * @param string $name
     * @param string $value
     * @param array $params
     * @param array $attr
     * @return string
     */
    public function getHTMLEditable($name, $value, $params, $attr = array()) {
        $viewMode = getValueFromArray($params, Flag::MULTISELECT_RENDER_MODE, 'checkboxes');
        return $this->{$viewMode}($name, $value, $params, $attr);
    }
    
    protected function checkboxes($name, $value, $params, $attr) {
        $template = PATH_DEFAULT . '/view/partials/checkboxes.mustache';
        
        $params = $this->normalizeParams($params);
        $dao = $this->getDAO($params);
        $dados = $dao->listAll();
        
        $vetor["list"] = $dados;
        $vetor["param_name"] = $name;
        return Template::singleton()->renderHTML(file_get_contents($template), $vetor);
    }
    
    /**
     * 
     * nao usar por enquanto, falta definir a forma de receber os dados
     * na submissão
     */
    protected function chosen($name, $value, $params, $attr) {
        MemoryPage::addCss("static/bstemplates/plugins/chosen/chosen.min.css");
        MemoryPage::addJs("static/bstemplates/plugins/chosen/chosen.jquery.min.js");
        
        $params = $this->normalizeParams($params);
        $dao = $this->getDAO($params);
        $dao_label = getValueFromArray($params, Flag::DAO_LABEL, Flag::DAO_LABEL);
        $dao_value = getValueFromArray($params, Flag::DAO_VALUE, Flag::DAO_VALUE);
        $dados = $dao->listAll();
        
        //$attr["value"] = $value;
        //$attr = $this->getHTMLAttributes($attr, $params);
        
        $template = PATH_APP . '/view/bstemplates/multiselect.mustache';
        
        $vetor["multiselect"] = [
            "placeholder" => getValueFromArray($params, Flag::PLACEHOLHER, "Escolha uma opção"),
            'options' => $dados
        ]; 
        return Template::singleton()->renderHTML(file_get_contents($template), $vetor);
    }
}
