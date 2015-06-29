<?php

namespace MyFrameWork\DataBase;

use MyFrameWork\Factory;
use PDOException;
use PDO;

/* 
 * Classe de banco genérica que herda de PDO
 */
abstract class DataBase extends PDO {
    
    /**
     *
     * @var \PDOStatement 
     */
    protected $statement;
    
    protected $user;
    protected $pass;
    protected $dbname;
    protected $host;
    protected $port;
    
    /**
     * @return string
     */
    protected abstract function getDsn();
    
    /**
     * Retorna o objeto query para o banco de dados
     * 
     * @return MyFrameWork\DataBase\Query
     */
    public abstract function getQuery();
    
    /**
     * Cria a conexão com o banco de dados.
     * Os erros são logados no diretório configurado no log4php.
     * 
     * @param string $host      O ip ou a url onde o banco de dados se encontra hospedado
     * @param string $dbname    O nome da base de dados a ser utilizada
     * @param string $user      O nome de usuário 
     * @param string $password  A senha do usuário
     * @param int    $port      A porta na qual o banco de dados está configurado
     */
    public function __construct($host, $dbname, $user, $password, $port=null) {
        $this->host   = $host;
        $this->dbname = $dbname;
        $this->user   = $user;
        $this->pass   = $password;
        $this->port   = $port;
        
        try {
            parent::__construct($this->getDsn(), $this->user, $this->pass, array());
            $this->setAttribute(self::ATTR_ERRMODE, self::ERRMODE_EXCEPTION);
        }
        catch (PDOException $e) {
            Factory::log()->info($e->getMessage());
            Factory::log()->fatal("[".date('H:i:s')."]Falha ao conectar-se com o banco de dados erro: {$e->getMessage()}", $e);
        }
    }
    
    /**
     * Prepara e executa uma query no banco de dados
     * 
     * @param  string $sql    Comando Select em forma de string
     * @param  array  $params Parametros a serem ligados
     * @return int            Retorna 0 para falso ou o número de linhas afetadas
     */
    public function execute($sql, $params=array()) {
        try {
            $this->statement = $this->prepare($sql);
            Factory::log()->debug($this->statement->queryString . "\n" . var_export($params, true));
            if (!is_array($params)) {
                $params = array($params);
            }
            if ($this->statement->execute($params)) {
                return $this->statement->rowCount();
            }
        } catch (PDOException $e) {
            Factory::log()->info($e->getMessage());
            Factory::log()->fatal("[".date('H:i:s')."]Falha ao executar uma query\nerror: {$e->getMessage()}", $e);
            return $e->getMessage();
        }
        return 0;
    }
    
    /**
     * Realiza uma consulta no banco e retorna todos os dados encontrados
     * 
     * @param  string $sql Comando Select em forma de string
     * @return array
     */
    public function fetchAll($sql, $params=array()) {
        if ($this->execute($sql, $params)) {
            $this->statement->setFetchMode(PDO::FETCH_ASSOC);
            $result = $this->statement->fetchAll();
            return $result;
        }
        return array();
    }
    
    /**
     * Retorna uma única linha da consulta ou o próximo resultado
     * 
     * @param  string $sql    Comando Select em forma de string
     * @param  array  $params Parametros a serem ligados
     * @return array
     */
    public function fetch($sql=null, $params=array()) {
        if (is_null($sql)) {
            # TODO remove this coments when push all commits to github server
            /* WARNING: fetch() does NOT adhere to SQL-92 SQLSTATE standard when dealing with empty datasets.
            Instead of setting the errorcode class to 20 to indicate "no data found", it returns a class of 00 indicating success, and returns NULL to the caller.
            This also prevents the exception mechainsm from firing.*/
            $returnValue = $this->statement->fetch();
            return !is_null($returnValue) ? $returnValue : array();
        }
        if ($this->execute($sql, $params)) {
            $this->statement->setFetchMode(\PDO::FETCH_ASSOC);
            $returnValue = $this->statement->fetch();
            return !is_null($returnValue) ? $returnValue : array();
        }
        return array();
    }
    
    /**
     * Retorna um único valor para uma consulta
     * 
     * @param  string $sql    Comando Select em forma de string
     * @param  array  $params array indexado com os valores dos parâmetros que serão ligados na consulta
     * @return mixed
     */
    public function fetchField($sql=null, $params=array()) {
        if (is_null($sql)) {
            $result = $this->statement->fetch(\PDO::FETCH_NUM);
            if (isset($result[0])) {
                return $result[0];
            }
        }
        if ($this->execute($sql, $params)) {
            $result = $this->statement->fetch(\PDO::FETCH_NUM);
            if (isset($result[0])) {
                return $result[0];
            }
        }
        return null;
    }
    
    /**
     * Retorna a quantidade de linhas afetadas pelo ultimo DELETE, INSERT ou UPDATE.
     * Se o ultimo comando foi um SELECT alguns bancos retornam o numero de linhas da consulta,
     * porem este comportamento não é garatido por todos os bancos de dados.
     * 
     * @return int
     */
    public function rowCount(){
        return $this->statement->rowCount();
    }
    
    /**
     * Realiza uma inserção no banco de dados
     * 
     * @param string $table Nome da tabela
     * @param array  $attrs Array associativo mapeando chave => valor, para atributo => valor
     * 
     *    example
     *    $db->insert("tabela1", ['campo1' => 'valor1', 'campo2' => 'valor2']);
     * 
     * @return int
     */
    public function insert($table, $attrs) {
        $ret = $this->getQuery()->insert($table, $attrs);
        if ($ret['status']) {
            return $this->execute($ret['sql'], $ret['values']);
        }
        return 0;
    }
    
    /**
     * Realiza um update no banco de dados
     * Se não houver uma condição WHERE por segurança não será executado o comando
     * 
     * @param string $table O nome da tabela
     * @param array  $attrs Array associativo mapeando chave => valor, para atributo => valor
     * @param mixed  $where Array associativo no formato wherearray ou um objeto Where
     * 
     *   example
     *   $db->update("tabela1", ['campo1' => 1, 'campo2' => 2], ['id' => 33])
     * 
     * @return int         retorna 0 para falso ou > 0 para true   
     */
    public function update($table, $attrs, $where) {
        $ret = $this->getQuery()->update($table, $attrs, $where);
        if ($ret['status']) {
            return $this->execute($ret['sql'], $ret['values']);
        }
        return 0;
    }
    
    /**
     * Realiza um delete no banco de dados
     * Se não houver uma condição WHERE por segurança não será executado o comando
     * 
     * @param string $table O nome da tabela
     * @param mixed  $where Array associativo no formato wherearray ou um objeto Where
     * 
     *   example
     *   $db->delete("tabela1", ['id' => 33])
     * 
     * @return mixed        Retorna 0 em caso de falha e boolean em caso de sucesso    
     */
    public function delete($table, $where) {
        try{
            $ret = $this->getQuery()->delete($table, $where);
            if ($ret['status']) {
                return $this->execute($ret['sql'], $ret['values']);
            }
            return 0;
        }catch(PDOException $e) {
            Factory::log()->info("[info]error ao deletar na camada DataBase: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Realiza uma consulta SQL no banco de dados trazendo todas as linhas da tabela
     * 
     * @param array  $columns Vetor com as colunas utilizadas na consulta
     * @param string $table   Nome da tabela
     * @param mixed  $where   Array associativo no formato wherearray, um objeto Where ou uma string
     * @param mixed  $extra   Array ou Expression Se $where for uma string $extra será considerado parâmetros
     * 
     *   example
     *   $db->select('*', "tabela1", ['id' => 33])
     * 
     * @return array    
     */
    public function select($columns, $table, $where=array(), $extra=array()) {
        $ret = $this->getQuery()->select($columns, $table, $where, $extra);
        if ($ret['status']) {
            return $this->fetchAll($ret['sql'], $ret['values']);
        }
        return array();
    }
    
    /**
     * Realiza uma consulta SQL no banco de dados retornando somente UMA linha da tabela
     * 
     * @param array  $columns  Vetor com as colunas utilizadas na consulta
     * @param string $table    Nome da tabela
     * @param mixed  $where    Array associativo no formato wherearray, um objeto Where ou uma string
     * @param mixed  $extra    Array ou Expression Se $where for uma string $extra será considerado parâmetros
     * 
     *   example
     *   $db->selectOne('*', "tabela1", ['id' => 33])
     * 
     * @return array  
     */
    public function selectOne($columns, $table, $where=array(), $extra=array()) {
        if (!is_string($where)) {
            if (is_array($extra)) {
                $extra['limit'] = 1;
            }
            else if (is_a($extra, 'Expression')) {
                $extra->setLimit(1);
            }
        }
        $ret = $this->getQuery()->select($columns, $table, $where, $extra);
        if ($ret['status']) {
            return $this->fetch($ret['sql'], $ret['values']);
        }
        return array();
    }
    
    /**
     * 
     * @return \PDOStatement
     */
    public function getStatement() {
        return $this->statement;
    }
    
    /**
     * Exibe informações referente aos parâmetros utilizados na conexão com o banco de dados
     * 
     * @readonly
     * @return string  
     */
    public function toString(){
        return 'Database ' . get_class($this) . '\n' .
                  'Pass ' . $this->pass . '\n' .
                  'user ' . $this->user . '\n' .
                  'host ' . $this->host . '\n' .
                  'Port ' . $this->port . '\n' .
                  'dbname ' . $this->dbname;
    }
}