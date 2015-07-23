<?php

namespace MyFrameWork\Request;

use MyFrameWork\Enum\RequestType;
use MyFrameWork\Enum\ResponseType;
use MyFrameWork\Factory;
use MyFrameWork\Session;
use MyFrameWork\Memory\MemoryPage;
use MyFrameWork\LoggerApp;

/* 
 * Classe genérica para todas as paginas contindas em page/*
 * Gerencia a requisição e a resposta para o cliente (processa a requisição)
 */
abstract class ProcessRequest {
    /**
     * Nome do método que foi chamado para renderizar a página
     * @var string
     */
    protected  $method;
    
    /**
     * Nome do método default que será chamado quando o método solicitado não existir
     * @var string
     */
    protected $defaultMethod = '_index';
    
    /**
     * Tipo da requisição
     * @var [GET|POST]
     */
    protected static $requestType;
    
    /**
     * Tipo da resposta
     * @var string ResponseType constant
     */
    protected $responseType;
    
    /**
     * Define se irá exibir os cabeçalhos e rodapés HTML, se a resposta for do tipo HTML
     * @var boolean 
     */
    protected $htmlFull = true;
    
    /**
     * Define o nome da página
     * @var String
     */
    protected $pageTitle = "";
    
    /**
     * O nome do template que irá renderizar a página
     * @var string 
     */
    protected $filename;
    
    /**
     * Conteúdo passado para o render
     * @var array
     */
    protected $pagedata = array();
    
    /**
     * Lista dos parâmetros utilizado na limpeza dos dados e geração do conteúdo dinâmicamente
     * @var array 
     */
    protected $parametersMeta = array(RequestType::GET => array(), RequestType::POST => array());
    
    /**
     * Lista dos valores dos parâmetros após a limpeza dos dados
     * @var array
     */
    protected $parametersValue = array();
    
    /**
     * Define se o render deve ou não ser chamado
     * @var boolean 
     */
    protected $render = true;
    
    /**
     * Identificador unico de um objeto
     * @var mixed
     */
    protected $id;
    
    /**
     * Define as regras de acesso
     * @var array - Os indices são os métodos e os valores são os grupos
     */
    protected $grantsRules = array();
    
    /**
     * Método de callback chamado antes do processamento
     */
    protected function preProcess() {}
    
    /**
     * Método de callback chamado após o processamento
     */
    protected function posProcess() {}
    
    
    /**
     * Seta o identificador único
     * @param int $id
     */
    public function setId($id) {
        $this->id = $id;
    }
    
    /**
     * Configura o arquivo template(.mustache)
     * @param string $templateName
     */
    public function setTemplateFile($templateName){
        $this->filename = $templateName;
    }
    
    /**
     * Método main implementado por padrão
     * @return boolean
     */
    protected function _main() {
        return true;
    }
    
    /**
     * Retorna o nome do arquivo de template
     * @return type
     */
    protected function getFilename() {
        return $this->filename;
    }
    
    /**
     * Exibe a mensagem de erro
     * @param Error $erro
     */
    public function setError(Error $erro) {
        //TODO
    }
    
    /**
     * Retorna o nome da URL chamada
     */
    protected function getPageName() {
        return $_GET['_page'];
    }
    
    /**
     * Verifica se é uma requisição GET
     * @return boolean
     */
    public static final function isGETRequest() {
        return self::$requestType == 'GET';
    }
    
    /**
     * Verifica se é uma requisição POST
     * @return boolean
     */
    public static final function isPOSTRequest() {
        return self::$requestType == 'POST';
    }
    
    /**
     * Retorna qual o método utilizado na requisição atual
     * @return string GET ou POST
     */
    public static final function getMethod() {
        if (empty(self::$requestType)) {
            //return filter_input(INPUT_SERVER, "REQUEST_METHOD");
            //$requestMethod = filter_input(INPUT_SERVER, "REQUEST_METHOD");
            //filter_input usando INPUT_SERVER nao funciona no php 5.5.14 (essa é a versao na locaweb) devido a um bug
            //https://github.com/wp-stream/stream/issues/254
            //https://bugs.php.net/bug.php?id=49184
            //https://bugs.debian.org/cgi-bin/bugreport.cgi?bug=730094
            return $_SERVER["REQUEST_METHOD"];
        }
        return self::$requestType;
    }
    
    /**
     * Processa a requisição e renderiza a página
     * @param string $requestType GET, POST
     * @param string $action Refere-se ao método
     * @param string $responseType Tipo de retorno da pagina, JSON, PDF etc.
     */
    public final function service($requestType, $action, $responseType) {
        //Definindo o tipo de resposta
        if (!ResponseType::isValid($responseType)) {
            $responseType = ResponseType::getDefaultType();
        }
        $this->responseType = $responseType;
        
        //Definindo o método de requisição
        if (!RequestType::isValid($requestType)) {
            $requestType = RequestType::GET;
        }
        self::$requestType = $requestType;
        
        $this->cleanParameters();
        $this->_processRequest($action);
        if ($this->render) {
            $this->render();
        }
    }
    
    /**
     * Método interno utilizado para realizar o procesamento da ação solicitada
     * @param type $action
     */
    private final function _processRequest($action) {
        $this->method = $action;
       
        //Define o nome do método que deverá ser chamado
        $method = '_' . $action;
        if (!method_exists($this, $method)) {
            if (method_exists($this, $this->defaultMethod)) {
                $method = $this->defaultMethod;
            }
            else {
                $method = '';
            }
        }
        
        if (empty($method)) {
            $errorPage = Factory::page('ErrorPage');
            $errorPage->setErrorMessage('O Método solicitado: "' . $action . '" não foi implementado');
            return $errorPage;
        }
        else {
            $this->preProcess();
            if ($this->canAccess()) {
                $this->executeMethod($method);
            }
            else {
                //permissão negada
                $deniedaccess = Factory::page("DeniedacessPage");
                $deniedaccess->service(ProcessRequest::getMethod(), "_index", ResponseType::getDefaultType());
                exit;
            }
            $this->posProcess();
        }
    }
    
    /**
     * Verifica se o usuário possui permissão para acessar o método solicitado
     * Se não há regras definidas para o método, o mesmo será liberado
     * 
     * @return boolean
     */
    protected function canAccess() {
        if(empty($this->method)) {
            $this->method = $this->defaultMethod;
        }
        if (startsWith($this->method, '_')) {
            $this->method = substr($this->method, 1);
        }
        
        $allowed = getValueFromArray($this->grantsRules, $this->method, array('*'));
        $login = Session::getInstance();
        
        if (in_array('*', $allowed)) {
            return true;
        }
        
        return count(array_intersect($login->getGroups(), $allowed)) > 0;
    }
    
    /**
     * Executa o método dinâmicamente e configura e seta as configurações do render
     * @param string $method Nome do método que deverá ser chamado
     */
    private function executeMethod($method) {
        $result = $this->$method();
        if (is_array($result)) {
            $this->pagedata = array_merge($this->pagedata, $result);
            $this->render = true;
        }
        else {
            $this->render = !is_null($result) && $result;
        }
        if ($this->render && empty($this->filename)) {
            $classFCNS = strtolower(get_class($this));
            
            $slashExploded = explode("\\", $classFCNS);
            $this->filename = $slashExploded[count($slashExploded)-1] . $method;
        }
    }
    
    /**
     * Renderiza a página exibindo o seu resultado no navegador
     */
    protected function render() {
        $response = Factory::response($this->responseType);
        $response->setHeader();
        $response->renderContent($this->getPagedata(), $this->getFilename());
    }
    
    /**
     * Retorna os dados gerados pela página (resultado de todo o processamento da página)
     * 
     * @return array
     */
    public function getPagedata() {
        if ($this->responseType == ResponseType::HTML && $this->htmlFull) {
            $this->pagedata = array_merge(
                $this->pagedata,
                array(
                    'html' => array(
                        'pagetitle' => MemoryPage::getTitle(trim(PAGE_TITLE_PREFIX . ' ' . $this->pageTitle)),
                        'urlbase' => DOMAIN,
                        'css' => MemoryPage::getCss(),
                        'js' => MemoryPage::getJs(),
                        'extraheader' => MemoryPage::getExtraHeader()
                    ),
                    '_page' => get_class($this),
                    '_action' => $this->method
                )
            );
        }
        
        $this->pagedata = array_merge($this->pagedata, MemoryPage::getAttributes());
        $s = Session::getInstance();
        if ($s->isLogged()) {
            $this->pagedata['isLogged'] = true;
            $this->pagedata['isAdmin'] = $s->isAdmin();
            foreach ($s->getGroups() as $group) {
                $this->pagedata['is' . ucfirst($group)] = true;
            }
            $this->pagedata['session'] = array(
                'id' => $s->getUserId(),
                'nome' => $s->getUserName(),
                'email' => $s->getData('email')
            );
        }
        $this->pagedata[SERVER_MODE] = true;
        $this->pagedata['error'] = LoggerApp::getErrors();
        //$this->pagedata["___get___"] =  $_GET;
        return $this->pagedata;
    }
    
    /**
     * Libera um método para um grupo ou um conjunto de grupos
     * @param string $method Nome do método
     * @param array|string $groups Nome do grupo ou array de grupos
     */
    protected function allowMethod($method, $groups) {
        if (!is_array($groups)) {
            $groups = array($groups);
        }
        if (startsWith($method, '_')) {
            $method = substr($method, 1);
        }
        $this->grantsRules[$method] = array_merge(
            getValueFromArray($this->grantsRules, $method, array()), $groups
        );
    }
    
    /**
     * Realiza a limpeza dos parâmetros eliminando os valores inválidos e setando os valores default
     */
    protected function cleanParameters() {
        $log = Factory::log();

        if (self::isPOSTRequest()) {
            $methods = array(RequestType::POST, RequestType::GET);
        }
        else {
            $methods = array(RequestType::GET);
        }
        foreach ($methods as $method) {
            foreach ($this->parametersMeta[$method] as $itemname => $itemdata) {
                if (isset($this->parametersValue[$itemname])) {
                    $log->info('Não é possível receber o parâmetro: ' . $itemname . ' por POST/GET');
                    continue;
                }
                
                $received = filter_input(RequestType::getInternalInput($method), $itemname, FILTER_UNSAFE_RAW);
                
                $log->debug('$_' . $method . '["' . $itemname . '"]: ' . var_export($received, true));                
                $type = Factory::datatype($itemdata['type']);
                
                if($type->isExpectedToBeArray()) {
                    $received = filter_input_array(RequestType::getInternalInput($method), array($itemname => array('flags' => FILTER_REQUIRE_ARRAY)));
                }
                
                $cleaned = $type->sanitize($received, $itemdata['params']);//faz os filtros, verifica os tipos e tals
                if (!$type->isValid($cleaned, $itemdata['params'])) {//chama os metodos isValid* da biblioteca respect/Validator
                    $this->parametersMeta[$method][$itemname]['error'] = LoggerApp::getLastError();
                    continue;
                }
                if (is_bool($cleaned)) {
                    $this->parametersValue[$itemname] = var_export($cleaned, true);
                }
                else {
                    $this->parametersValue[$itemname] = $cleaned;
                }
            }
        }
    }
    
    /**
     * Adiciona uma lista de parâmetros para um determinado valor
     * Se o parâmetro existe ele será sobrescrito
     * 
     * @param string $name Nome do parâmetro
     * @param string $type Nome do tipo do parâmetro (Deve ser um Datatype)
     * @param array $params Lista de parâmetros
     * @param string $method Tipo do método (GET ou POST) se nenhum for informado o método padrão é utilizado
     * @return ProcessRequest
     */
    protected final function addParameter($name, $type, $params=array(), $method=null) {
        //Se o tipo é válido
        if (Factory::datatype($type) != null) {
            if (!RequestType::isValid($method)) {
                $method = self::getMethod();
            }
            $this->parametersMeta[$method][$name] = array('type' => $type, 'params' => $params);
        }
        return $this;
    }
    
    /**
     * Remove um parametro da lista de parâmetros
     * @param string $name
     * @param string $method Tipo do método (GET ou POST) se nenhum for informado o método padrão é utilizado
     */
    protected final function removeParameter($name, $method=null) {
        if (!RequestType::isValid($method)) {
            $method = self::getMethod();
        }
        if (isset($this->parametersMeta[$method][$name])) {
            unset($this->parametersMeta[$method][$name]);
        }
        if (isset($this->parametersValue[$name])) {
            unset($this->parametersValue[$name]);
        }
    }
    
    /**
     * Retorna o valor de um parâmetro já validado
     * @param string $name Nome do parâmetro
     * @param mixed $default Valor default que será retornado se $name for nulo
     * @return mixed Valor recebido ou o valor padrão
     */
    protected final function getParameter($name, $default=null) {
        return getValueFromArray($this->parametersValue, $name, $default);
    }
    
    /**
     * Retorna se os parâmetros recebidos são válidos
     * @return boolean
     */
    protected function isParametersValid() {
        foreach ($this->parametersMeta as $params) {
            foreach ($params as $data) {
                if (isset($data['error'])) {
                    return false;
                }
            }
        }    
        return true;
    }
    
    /**
     * Limpa e valida os parametros e os retorna se são válidos ou não
     * @return boolean
     */
    protected function isValidParameters() {
        $this->cleanParameters();
        foreach ($this->parametersMeta as $params) {
            foreach ($params as $data) {
                if (isset($data['error'])) {
                    return false;
                }
            }
        }    
        return true;
    }
    
    public function getErrorByParameterName(array $parametersMeta) {
        foreach ($parametersMeta as $params) {
            foreach ($params as $key => $data) {
                if (isset($data['error'])) {
                    $this->pagedata["{$key}_error"] = $data["error"];
                }
            }
        }
    }
}
