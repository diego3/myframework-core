<?php

namespace MyFrameWork\Email;

use MyFrameWork\Email\AbstractEmail;
use MyFrameWork\Email\HtmlInterface;


/**
 * Implementação de um email que usa o corpo em html
 *
 * @author Diego Rosa dos Santos <diegosantos@alphaeditora.com.br>
 */
class EmailHtml extends AbstractEmail implements HtmlInterface {
    /**
     * Armazena o caminho para o template html
     * 
     * @var string 
     */
    protected $template;
    
    /**
     * Variaveis mustache
     * 
     * @var array 
     */
    protected $params;
    
    /**
     * Retorna o caminho para o template html
     * 
     * @return string 
     */
    public function getTemplatePath() {
        if(empty($this->template)){
            return "";
        }
        return $this->template;
    }
    
    /**
     * 
     * @param string $template
     * @throws \InvalidArgumentException
     */
    public function setTemplatePath($template) {
        if(!endsWith($template, ".mustache")) {
            throw new \InvalidArgumentException("O arquivo de template do email deve ser do tipo .mustache");
        }
        $this->template = $template;
    }

    public function setTemplateParams($params) {
        $this->params = $params;
    }
    
    public function  getTemplateParams(){
        return $this->params;
    }

}
