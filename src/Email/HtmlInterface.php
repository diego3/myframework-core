<?php

namespace MyFrameWork\Email;

/**
 * Implementar essa interface significa que o email será em html e usuará um template mustache para montar o corpo do mesmo.
 * 
 * @author Diego Rosa dos Santos <diegosantos@alphaeditora.com.br>
 */
interface HtmlInterface {
    /**
     * Retorna o caminho do arquivo de template do email
     * 
     * O arquivo de template DEVE ser um .mustache, pois ele usado para renderizar o html
     * 
     * @return string 
     */
    public function getTemplatePath();
    
    /**
     * Configura o arquivo de template dinamicamente.
     * 
     * O arquivo de template DEVE ser um .mustache, pois ele usado para renderizar o html
     * 
     * @param string $template O caminho para o arquivo
     * @throws \InvalidArgumentException  Ter certeza que o template é .mustache
     */
    public function setTemplatePath($template);
    
    public function setTemplateParams($params);
    
    public function getTemplateParams();
    
}
