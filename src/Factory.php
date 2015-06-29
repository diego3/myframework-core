<?php

namespace MyFrameWork;

use MyFrameWork\Event\EventManager,
    MyFrameWork\Event\PageEventManager;

use MyFrameWork\DataBase\PgDataBase;
use MyFrameWork\DataBase\MyDataBase;
use MyFrameWork\Memory\Memory;
use MyFrameWork\Response\EmptyResponse;
use Logger;
use ReflectionClass;

/* 
 * Classe factory geradora de objetos
 */
class Factory {
    
    /**
     * Cria um objeto do tipo response
     * 
     * @param string $type Nome do response
     * @return \Response
     */
    public static function response($type) {
        $className = ucfirst(strtolower($type));
        if (!endsWith($className, 'Response')) {
            $className .= 'Response';
        }
        $fileName = PATH_MYFRAME . '/src/Response/' . $className . '.php';
       
        if (file_exists($fileName)) {
            require_once($fileName);
            $factory = new ReflectionClass('MyFrameWork\\Response\\'.$className);
            if ($factory->implementsInterface('MyFrameWork\Response\Response')) {
                return $factory->newInstance();
            }
            // @codeCoverageIgnoreStart
        }
        else {
            //TODO log this error
            echo "O arquivo {$fileName} do tipo Response não existe";
        }
        // @codeCoverageIgnoreEnd
       
       return new EmptyResponse();
    }
    
    /**
     * Cria e retorna um objeto para a página solicitada
     * 
     * @param  string          $page Nome da página que irá processar a solicitação
     * @return \ProcessRequest
     */
    public static function page($page) {
        $className = ucfirst($page);
        if (file_exists(PATH_APP . '/Page/' . $className . '.php')) {
            //Carrega uma página da aplicação
            Memory::set('template', 'app');
            $fileName = PATH_APP . '/Page/' . $className . '.php';
        }
        else if (file_exists(PATH_DEFAULT . '/Page/' . $className . '.php')) {
            //Carrega uma página default do framework
            Memory::set('template', 'default');
            $fileName = PATH_DEFAULT . '/Page/' . $className . '.php';
        }
        else {
            return self::page('ErrorPage');
        }
        
        require_once($fileName);
        
        
        $factory = new \ReflectionClass("Application\\Page\\".$className);
        
        if($factory->implementsInterface("MyFrameWork\Event\PublisherInterface")) {
            $instance = $factory->newInstance();
            $method   = $factory->getMethod("setEventManager");
            $method->invoke($instance, EventManager::getInstance());
            return $instance;
        }
        else if($factory->implementsInterface("MyFrameWork\Event\PagePublisherInterface")) {
            
            return $factory->newInstance(PageEventManager::getInstance());
        }
        else if ($factory->hasMethod('service') && !$factory->isAbstract()) {
            
            return $factory->newInstance();
        }
        else {
            // @codeCoverageIgnoreStart
            //Só entra neste else caso exista alguma classe que não seja Page na pasta dos pages
            return self::page('ErrorPage');
            // @codeCoverageIgnoreEnd
        }
    }
    
    /**
     * Armazena conexoes com o banco de dados
     * @var array 
     */
    private static $connections = array();
    
    /**
     * Cria conexões únicas com banco de dados, conforme o driver especificado
     * 
     * @param array $params Array associativo contendo os seguintes dados:
     *      driver - Banco de dados que será utilizado. Valores suportados: pgsql, mysql
     *      dbname - Nome do banco de dados
     *      host - Endereço do banco de dados
     *      port - Porta utilizada pelo banco
     *      user - Nome do usuário do banco
     *      password - Senha do banco de dados
     * Se nenhum parâmetro for informado será criado uma conexão padrão, obtida pelas configurações
     * @return DataBase Um objeto de conexao com o banco de dados
     */
    public static function database($params=array()){
        if (empty($params)) {
            $params['driver'] = DATABASE_DRIVER;
            $params['dbname'] = DATABASE_NAME;
            $params['host'] = DATABASE_HOST;
            $params['port'] = DATABASE_PORT;
            $params['user'] = DATABASE_USER;
            $params['password'] = DATABASE_PASSWORD;
        }

        //Validate
        if (empty($params['driver']) || empty($params['dbname']) || empty($params['user'])) {
            Factory::log()->error('Não é possível conectar-se com o banco de dados: "É necessário informar o driver, nome do banco e usuário"');
            return null;
        }

        $host = getValueFromArray($params, 'host');
        $dbname = getValueFromArray($params, 'dbname');
        $user = getValueFromArray($params, 'user');
        $password = getValueFromArray($params, 'password');
        $port = getValueFromArray($params, 'port');
        $idx = md5($host.$dbname.$user.$password);
        if (isset(self::$connections[$idx])) {
            return self::$connections[$idx];
        }

        switch ($params['driver']) {
            case 'pgsql':
                require_once PATH_MYFRAME . '/src/DataBase/Postgresql.php';
                self::$connections[$idx] = new PgDataBase($host, $dbname, $user, $password, $port);
                break;
            
            case 'mysql':
                require_once PATH_MYFRAME . '/src/DataBase/Mysql.php';
                self::$connections[$idx] = new MyDataBase($host, $dbname, $user, $password, $port);
                break;
            
            default:
                self::log()->error('Não é possível conectar-se com o banco de dados: "Driver {' . $params['driver'] . '} não suportado"');
                return null;
        }

        return self::$connections[$idx];
    }

    /**
     * Armazena os DAOS gerados
     * @var array 
     */
    private static $daos = array();
    
    /**
     * Instancia a classe DAO
     * @param string $daoName Nome da classe DAO
     * @param DataBase $db Database que deverá ser utilizada na criação do DAO
     * @return \DAO
     */
    public static function DAO($daoName, $db=null) {
        $className = ucfirst($daoName);
        if (!endsWith($daoName, 'DAO')) {
            $className .= 'DAO';
        }
        if (is_null($db)) {
            $daokey = $className;
        }
        else {
            $daokey = $className . $db->toString();
        }
        if (!isset(self::$daos[$daokey])) {
            self::$daos[$daokey] = null;
            // @codeCoverageIgnoreStart
            if (file_exists(PATH_APP . '/Model/Dao/' . $className . '.php')) {
                require_once(PATH_APP . '/Model/Dao/' . $className . '.php');
            }
            // @codeCoverageIgnoreEnd
            else if (file_exists(PATH_DEFAULT . '/Model/Dao/' . $className . '.php')) {
                require_once(PATH_DEFAULT . '/Model/Dao/' . $className . '.php');
            }
            else {
                self::log()->info('A classe DAO " ' . $daoName . '" não existe');
                return null;
            }
            $factory = new ReflectionClass("Application\\Model\\Dao\\" . $className);
            
            if($factory->isSubclassOf('MyFrameWork\\DataBase\\DAO') and $factory->implementsInterface("MyFrameWork\Event\PublisherInterface")) {
                $instance = $factory->newInstance($db);
                $method   = $factory->getMethod("setEventManager");
                $method->invoke($instance, EventManager::getInstance());
                return $instance;
            }
            else if ($factory->isSubclassOf('MyFrameWork\\DataBase\\DAO') && !$factory->isAbstract()) {
                self::$daos[$daokey] = $factory->newInstance($db);
            }
            else {
                // @codeCoverageIgnoreStart
                //Só entra neste else caso exista alguma classe que nãos seja DAO na pasta dos DAOS
                self::log()->info('A classe DAO " ' . $daoName . '" não é válida');
                return null;
                // @codeCoverageIgnoreEnd
            }
        }
        return self::$daos[$daokey];
    }
    
    /**
     * Return/Create the Logger object
     * 
     * Levels:
     * $log->trace("Finest-grained informational events.");
     * $log->debug("Fine-grained informational events that are most useful to debug an application.");
     * $log->info("Informational messages that highlight the progress of the application at coarse-grained level.");
     * $log->warn("Potentially harmful situations which still allow the application to continue running.");
     * $log->error("Error events that might still allow the application to continue running.");
     * $log->fatal("Very severe error events that will presumably lead the application to abort.");
     * 
     * @return Logger
     */
    public static function log() {
        if(defined("TEST_OR_INSTALL")) {
            require_once PATH_LOCAL . '/../vendor/apache/log4php/src/main/php/Logger.php';
        }
        else {
            require_once PATH_LOCAL . '/vendor/apache/log4php/src/main/php/Logger.php';
        }
        
        if (Memory::get('debug', false)) {
            return Logger::getLogger('debug');
        }
        else {
            return Logger::getLogger('main');
        }
    }

    /**
     * Armazena os datatypes carregados
     * @var array 
     */
    private static $datatypes = array();
    
    /**
     * Return the unique instance of datatype
     * @param string $name O tipo do Datatype a ser fabricado
     * @return Datatype
     */
    public static function datatype($name) {
        if (!startsWith($name, 'Datatype')) {
            $name = 'Datatype' . ucfirst($name);
        }
        if (!isset(self::$datatypes[$name])) {
            $fileName = PATH_MYFRAME . '/src/DataType/' . $name . '.php';
            self::$datatypes[$name] = null;
            if (file_exists($fileName)) {
                require_once($fileName);
                $factory = new ReflectionClass("MyFrameWork\\DataType\\".$name);
                if (!$factory->isAbstract()) {
                    self::$datatypes[$name] = $factory->newInstance();
                }
                else {
                    self::log()->info('O parâmetro [' . $name . '] não pode ser carregado');
                }
            }
            else {
                self::log()->info('O parâmetro [' . $name . '] não existe');
            }
        }
        return self::$datatypes[$name];
    }
    
    /**
     * Armazena os enums carregados
     * @var array 
     */
    private static $enums = array();
    
    /**
     * Cria e retorna um enum válido
     * @param $name O nome do Enum que deverá ser carregado
     * @return BasicEnum
     */
    public static function enum($name) {
        $name = ucfirst($name);
        if (!isset(self::$enums[$name])) {
            $fileName = PATH_MYFRAME . '/src/Enum/' . $name . '.php';
            $fileName2 = PATH_APP . '/Model/Enum/' . $name . '.php';
            self::$enums[$name] = null;
            if (file_exists($fileName)) {
                require_once($fileName);
                $factory = new ReflectionClass("MyFrameWork\\Enum\\" . $name);
            }
            else if (file_exists($fileName2)) {
                require_once($fileName2);
                $factory = new ReflectionClass($name);
            }
            if (!empty($factory)) {
                if (!$factory->isAbstract()) {
                    self::$enums[$name] = $factory->newInstance();
                }
                else {
                    self::log()->info('O enum [' . $name . '] não pode ser carregado');
                }
            }
            else {
                self::log()->info('O enum [' . $name . '] não existe');
            }
        }
        return self::$enums[$name];
    }
}
