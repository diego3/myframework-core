<?php

namespace MyFrameWork;

/* 
 * Classe que gerencia a sessão e o login
 */
class Session {
    /**
     * Lista de valores protegidos
     * @var array
     */
    protected $blocked = array();
    
    protected function __construct() {
        $this->blocked = array(
                        '_logged', 
                        '_userid', 
                        '_username', 
                        '_groups'
                    );
    }
    
    /**
     * Instancia única do login
     * @var Session
     */
    protected static $instance;
    
    // @codeCoverageIgnoreStart
    /**
     * Retorna uma instancia única da classe login
     * @return Session
     */
    public static function getInstance() {
        if (is_null(self::$instance)) {
            self::$instance = new Session();
        }
        return self::$instance;
    }
    // @codeCoverageIgnoreEnd
    
    /**
     * Attempt to sing in both portuguese-app and website systems
     * @param string $usuario Nome do usuário ou email
     * @param string $password Senha with hash, because in database store it with hash too
     * @return bool
     */
    public function login($usuario, $password) {
        try{
            return true;
        }catch(Exception $ex) {
            $_SESSION["logginError"] = $ex->getMessage();
            return false;
        }
    }
    
    /**
     * Realiza o logout 
     */
    public function logout() {
        $_SESSION['_logged'] = false;
        $_SESSION['_facebookSession'] = false;
        unset($_SESSION);
    }
    
    /**
     * Define o usuário corrente que está logado
     * @param int $id
     * @param string $name Nome do usuário
     * @param array $groups Lista de grupos que o usuário pertence
     * @param array $extra Dados extras para que sejam armazenados na sessão
     */
    public function setCurrentUser($id, $name, $groups, $extra=array()) {
        $_SESSION['_logged'] = true;
        $_SESSION['_userid'] = $id;
        $_SESSION['_username'] = $name;        
        $_SESSION['_groups'] = $groups;
        foreach ($extra as $k => $v) {
            $this->setData($k, $v);
        }
    }
    
    /**
     * Retorna o código do usuário
     * @return int ou null
     */
    public function getUserId() {
        if ($this->isLogged()) {
            return getValueFromArray($_SESSION, '_userid', null);
        }
        return null;
    }
    
    /**
     * Retorna o nome do usuário logado
     * @return string
     */
    public function getUserName() {
        if ($this->isLogged()) {
            return getValueFromArray($_SESSION, '_username', '');
        }
        return '';
    }
    
    /**
     * Retorna se o usuário pertence ao grupo informado
     * @param string $group
     * @return boolean
     */
    public function isMemberOf($group) {
        if (!$this->isLogged()) {
            return false;
        }
        if ($this->isAdmin()) {
            return true;
        }
        return in_array($group, $this->getGroups());
    }
    
    /**
     * Retorna a lista de grupos do usuário
     * @return array
     */
    public function getGroups() {
        if ($this->isLogged()) {
            return getValueFromArray($_SESSION, '_groups', array());
        }
        return array();
    }
    
    /**
     * Retorna se o usuário está logada ou não
     * @return boolean
     */
    public function isLogged() {
        return getValueFromArray($_SESSION, '_logged', false);
    }
    
    /**
     * Define se o usuário tem privilégios de admin
     * @return boolean
     */
    public function isAdmin() {
        return in_array('admin', $this->getGroups());
    }
    
    /**
     * Armazena um dado na sessão
     * @param string $key
     * @param mixed $value
     */
    public function setData($key, $value) {
        if (in_array($key, $this->blocked)) {
            Factory::log()->debug('A chave "' . $key . '" é um valor protegido e não pode ser alterado');
        }
        else {
            $_SESSION[$key] = $value;
        }
    }
    
    /**
     * Retorna um valor que está na sessão e não é protegido
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getData($key, $default=null) {
        if (in_array($key, $this->blocked)) {
            Factory::log()->debug('A chave "' . $key . '" é um valor protegido e não pode ser obtido diretamente');
            return $default;
        }
        return getValueFromArray($_SESSION, $key, $default);
    }
    
    /**
     * Limpa um valor da sessão
     * @param string $key
     */
    public function removeData($key) {
        if (in_array($key, $this->blocked)) {
            Factory::log()->debug('A chave "' . $key . '" é um valor protegido e não pode ser removido');
        }
        else if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }
    
    /**
     * Recupera o identificador da sessão
     * @return string
     */
    public function getSessionName() {
        return md5(session_id());
    }
    
    /**
     * 
     * @param string $keyName
     * @return mixed The cookie value or null of it is expired or not exists
     */
    public function getCookieValue($keyName){
        if(isset($_COOKIE[$keyName])) {
            return $_COOKIE[$keyName];
        }
        return null;
    }
    
    /**
     * 
     * @param string $keyName
     * @return boolean
     */
    public function removeCookie($keyName){
        setcookie($keyName);
        return true;
    }
    
    /**
     * 
     * @param string $key
     * @param string|int $value
     */
    public function createCookie43Days($key, $value) {
        setcookie($key, $value, (time() + (3 * 24 * 3600)));
    }
    
    /**
     * 
     * @param string $key
     * @param string|int $value
     */
    public function createCookie42Hours($key, $value) {
        setcookie($key, $value, (time() + (2 * 3600)));
    }
}
