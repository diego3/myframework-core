<?php

namespace MyFrameWork;

use MyFrameWork\Factory;

/* 
 * Automatiza o uso dos templates na aplicação
 * @see https://github.com/bobthecow/mustache.php
 */
class Template {
    private static $t = null;
    
    private $templatename = 'view';
    
    /**
     * Variável de instancia que representa o engine mustache do framework
     * 
     * @var \Mustache_Engine 
     */
    private $mustache;
    
    /**
     * Variável de instância que representa o engine mustache da aplicação
     * 
     * @var \Mustache_Engine
     */
    private $mustacheapp;
    
    /**
     * Mustache Engine para memória
     * 
     * @var \Mustache_Engine
     */
    private $mm;
    
    protected function __construct() {
        require_once PATH_LOCAL . '/vendor/mustache/mustache/src/Mustache/Autoloader.php';
        \Mustache_Autoloader::register();
        
        //mustache para o trabalhar com os templates do app_default
        $this->mustache = new \Mustache_Engine($this->getEngineData(PATH_DEFAULT . '/view'));
        //mustache para trabalhar com os templates do app
        $this->mustacheapp = new \Mustache_Engine($this->getEngineData(PATH_APP . '/' . $this->templatename));
        //mustache generico ?
        $this->mm = new \Mustache_Engine();
    }
    
    /**
     * Return an array with engine data
     * 
     * @return array
     */
    protected function getEngineData($pathTemplate) {
        $partials = new \Mustache_Loader_FilesystemLoader(PATH_DEFAULT . '/view/partials');
        
        //se o template não estiver no app_default
        if (!startsWith($pathTemplate, PATH_DEFAULT)) {
            //carregará os partials em cascata
            $partials = new \Mustache_Loader_CascadingLoader(
                array(
                    $partials, //app_default
                    new \Mustache_Loader_FilesystemLoader($pathTemplate)
                )
            );
        }
       
        return array(
            'template_class_prefix' => '__MyTemplates_',
            'cache' => PATH_TEMP . '/cache/mustache',
            'loader' => new \Mustache_Loader_FilesystemLoader($pathTemplate),
            'partials_loader' => $partials,
            'charset' => 'UTF-8',
        );
    }
    
    /**
     * Seleciona qual render do mustache deverá ser chamado
     * E retorna o template para o arquivo solicitado
     * 
     * @param string $filename Nome do arquivo que deverá ser carregado. O arquivo deve existir em PATH_APP . '/view/' . $filename . '.mustache'
     * @return \Mustache_Template
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
     * Retorna o conteúdo renderizado de um template.
     * NÃO imprime o conteúdo na tela.
     * 
     * @param  string  $filename O nome do arquivo de template
     * @param  array   $data     Os parâmetros passados para o template
     * @return string            Retorna o conteúdo renderizado
     */
    public function renderTemplate($filename, $data) {
        try {
            $tpl = $this->loadTemplate($filename);
            return $tpl->render($data);
        }
        catch (Exception $e) {
            Factory::log()->fatal(sprintf('Template::renderTemplate error on %s template file. message: ' . $e->getMessage(), $filename));
        }
        return "";
    }
    
    /**
     * Renderiza e IMPRIME o conteúdo na tela.
     * 
     * @param string $filename O nome do arquivo de template
     * @param array  $data     Os parâmetros passados para o template
     * @return void
     */
    public function showRenderTemplate($filename, $data) {
        echo $this->renderTemplate($filename, $data);
    }
    
    /**
     * Renderiza varias vezes o mesmo template e retorna um vetor com o conteúdo renderizado
     * NÃO imprime o resultado na tela.
     * 
     * @param  string $filename  O nome do arquivo de template
     * @param  array  $listdata  Um vetor de parâmetros que serão passados para o template
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
     * Renderiza várias vezes o mesmo template e IMPRIME o resultado na tela
     * 
     * @param string $filename  O nome do arquivo de template
     * @param array  $listdata  Um vetor de parâmetros que serão passados para o template
     * @param string $glute     O conteúdo adicionado entre os templates
     */
    public function showRenderLoopTemplate($filename, $listdata, $glute='') {
        echo join($glute, $this->renderLoopTemplate($filename, $listdata));
    }
    
    /**
     * Retorna o resultado do processamento.
     * NÃO imprime o conteúdo na tela.
     * 
     * @param  string $template  Strings simples com variáveis mustache. Pode ser o conteúdo de um arquivo inteiro também
     * @param  array  $params    Um vetor de parâmetros que serão passados para o template
     * @return string 
     */
    public function renderHTML($template, $params) {
        return $this->mm->render($template, $params);
    }
    
    /**
     * Retorna uma instancia de Template
     * 
     * @return \Template
     */
    public static function singleton() {
        if (self::$t === null) {
            self::$t = new Template();
        }
        return self::$t;
    }
    
    /**
     * Retorna uma instancia de Template
     * 
     * @return \Template
     */
    public static function getInstance() {
        if (self::$t === null) {
            self::$t = new Template();
        }
        return self::$t;
    }
}