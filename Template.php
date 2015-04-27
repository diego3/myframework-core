<?php

/* 
 * Automatiza o uso dos templates na aplicação
 * @see https://github.com/bobthecow/mustache.php
 */
class Template {
    private static $t = null;
    
    private $templatename = 'view';
    
    /**
     * Variável de instancia que representa o engine mustache do framework
     * @var Mustache_Engine 
     */
    private $mustache;
    
    /**
     * Variável de instância que representa o engine mustache da aplicação
     * @var Mustache_Engine
     */
    private $mustacheapp;
    
    /**
     * Mustache Engine para memória
     * @var Mustache_Engine
     */
    private $mm;
    
    protected function __construct() {
        require_once PATH_LOCAL . '/vendor/Mustache/Autoloader.php';
        Mustache_Autoloader::register();
        
        $this->mustache = new Mustache_Engine($this->getEngineData(PATH_DEFAULT . '/view'));
        $this->mustacheapp = new Mustache_Engine($this->getEngineData(PATH_APP . '/' . $this->templatename));
        $this->mm = new Mustache_Engine();
    }
    
    /**
     * Return an array with engine data
     * @return array
     */
    protected function getEngineData($pathTemplate) {
        $partials = new Mustache_Loader_FilesystemLoader(PATH_DEFAULT . '/view/partials');
        if (!startsWith($pathTemplate, PATH_DEFAULT)) {
            $partials = new Mustache_Loader_CascadingLoader(
                array($partials, new Mustache_Loader_FilesystemLoader($pathTemplate))
            );
        }
       
        return array(
            'template_class_prefix' => '__MyTemplates_',
            'cache' => PATH_TEMP . '/cache/mustache',
            'loader' => new Mustache_Loader_FilesystemLoader($pathTemplate),
            'partials_loader' => $partials,
            'charset' => 'UTF-8',
        );
    }
    
    /**
     * Seleciona qual render do mustache deverá ser chamado
     * E retorna o template para o arquivo solicitado
     * 
     * @param string $filename Nome do arquivo que deverá ser carregado
     * @return Mustache_Template
     */
    protected function loadTemplate($filename) {
        if (file_exists(PATH_APP . '/view/' . $filename . '.mustache')) {
            return $this->mustacheapp->loadTemplate($filename);
        }
        else {
            return $this->mustache->loadTemplate($filename);
        }
    }
    
    /**
     * Retorna o conteúdo renderizado de um template
     * @param string $filename O nome do arquivo de template
     * @param array $data Os parâmetros passados para o template
     * @return string
     */
    public function renderTemplate($filename, $data) {
        try {
            $tpl = $this->loadTemplate($filename);
            return $tpl->render($data);
        }
        catch (Exception $e) {
            //TODO logerror
            return 'error: ' . $e->getMessage();
        }
    }
    
    /**
     * Renderiza e exibe o resulado do processamento
     * @param string $filename O nome do arquivo de template
     * @param array $data Os parâmetros passados para o template
     */
    public function showRenderTemplate($filename, $data) {
        echo $this->renderTemplate($filename, $data);
    }
    
    /**
     * Renderiza varias vezes o mesmo template e retorna um vetor com o conteúdo renderizado
     * @param string $filename O nome do arquivo de template
     * @param array $listdata Um vetor de parâmetros que serão passados para o template
     * @return array
     */
    public function renderLoopTemplate($filename, $listdata) {
        try {
            $tpl = $this->loadTemplate($filename);
            $content = array();
            foreach ($listdata as $data) {
                $content[] = $tpl->render($data);
            }
            return $content;
         }
        catch (Exception $e) {
            Factory::log()->info($e->getMessage());
            return array();
        }
    }
    
    /** 
     * Renderiza varias vezes o mesmo template e imprime o resultado na tela
     * @param string $filename O nome do arquivo de template
     * @param array $listdata Um vetor de parâmetros que serão passados para o template
     * @param string $glute O conteúdo adicionado entre os templates
     */
    public function showRenderLoopTemplate($filename, $listdata, $glute='') {
        echo join($glute, $this->renderLoopTemplate($filename, $listdata));
    }
    
    /**
     * 
     * @param string $template
     * @param array $params
     * @return string
     */
    public function renderHTML($template, $params) {
        return $this->mm->render($template, $params);
    }
    
    /**
     * Retorna uma instancia de Tempalte
     * @return Template
     */
    public static function singleton() {
        if (self::$t == null) {
            self::$t = new Template();
        }
        return self::$t;
    }
}