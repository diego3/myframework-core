<?php

namespace MyFrameWork\DataBase;

use MyFrameWork\Factory;
use PDOException;

/* 
 * Classe DAO
 */
abstract class DAO {
    /**
     * Armazena a conexão com o banco de dados
     * 
     * @var \MyFrameWork\DataBase\DataBase
     */
    protected $db;
    
    /**
     * Armazena o nome da tabela
     * 
     * @var string
     */
    protected $tablename;
    
    /**
     * Armazena o(s) nome(s) do(s) campo(s) que é/são chave(s) primária(s) na tabela
     * 
     * @var string|array
     */
    protected $pks = 'id';
    
    /**
     * Define se a tabela possui um campo chamado "ativo" que habilita a exclusão lógica
     * 
     * @var boolean
     */
    protected $hasactive = true;
    
    /**
     * Armazena a lista de chaves estrangeiras
     * 
     * @var array O indice do vetor é o nome do campo e o valor é o nome da classe DAO referente
     */
    protected $fks;
    
    /**
     * Armazena a Lista de objetos DAO
     * 
     * @var \DAO[] array
     */
    protected $daos = array();
    
    /**
     * Cria o objeto DAO
     * 
     * @param \MyFrameWork\DataBase\DataBase $db
     */
    public function __construct($db=null) {
        if ($db) {
            $this->db = $db;
        }
        else {
            $this->db = Factory::database();
        }
        $this->setParams();
    }
    
    /**
     * Método que define o nome da tabela e demais dados (chave primária e campo ativo)
     */
    abstract protected function setParams();
    
    /**
     * Return the database object
     * 
     * @return \MyFrameWork\DataBase\DataBase
     */
    public function getDatabase() {
        return $this->db;
    }
    
    /**
     * Retorna o nome da tabela que o DAO irá controlar
     * 
     * @return string
     */
    public function getTableName() {
        return $this->tablename;
    }
    
    /**
     * Retorna o nome do campo que é chave primária na tabela
     * 
     * @return string|array
     */
    public function getPKFieldName() {
        return $this->pks;
    }
    
    /**
     * Define se a tabela possui um campo ativo fazendo a exclusão lógica ao invés da exclusão real
     * 
     * @return boolean
     */
    protected function hasAtivo() {
        return $this->hasactive;
    }
    
    /**
     * 
     * @param boolean $booleanValue
     */
    protected function setLogicExclusion($booleanValue) {
        $this->hasactive = $booleanValue;
    }
    
    /**
     * Cria a condição where
     * 
     * @param  mixed $id  Valor do campo ID, se for multiplo um vetor indexado pelo nome das chaves
     * @return array      Retorna array no formato WHERE
     */
    protected function createWhere($id) {
        if (is_array($this->pks)) {
            $where = array();
            foreach ($this->pks as $pk) {
                if (isset($id[$pk])) {
                    $where[$pk] = $id[$pk];
                }
                else {
                    return array();
                }
            }
            return $where;
        }
        else {
            return array($this->pks => $id);
        }
    }
    
    /**
     * Realiza uma inserção dos dados passados em values
     * 
     * @param  array $values Array associativo atributo => valor
     * @return int           Retorna 0 para falso ou > 0 para true
     */
    public function insert($values) {
        $this->preInsert($values);
        //TODO Restaurar dados se a tabela tiver campos unico e exclusão lógica estiver habilitado
        return $this->db->insert($this->getTableName(), $values);
    }
    
    /**
     * 
     * @param array $values
     */
    public function preInsert($values) {}
    
    /**
     * Realiza a alteração dos dados passados em values para o id
     * 
     * @param  array $values Array associativo atributo => valor
     * @param  mixed $id     Valor do campo ID, se for multiplo um vetor indexado pelo nome das chaves
     * @return int           Retorna 0 para falso ou > 0 para true
     */
    public function update($values, $id) {
        $this->preUpdate($values, $id);
        
        $where = $this->createWhere($id);
        if (empty($where)) {
            Factory::log()->warn('O campo informado para alteração é inválido');
            return 0;
        }
        return $this->db->update($this->getTableName(), $values, $where);
    }
    
    /**
     * 
     * @param array $values
     * @param int $id
     */
    public function preUpdate($values, $id) {}
    
    /**
     * Tenta inserir uma registro na tabela deste DAO.
     * No caso de violação de chaves primárias então o algoritimo tentará dar update.
     * 
     * @param  array  $data    Column-value array
     * @return mixed           Retorna o ID se for executado um insert, retorna 
     * quantidade de linhas afetadas caso seja executado um update ou a string 
     * com a mensagem de erro neste caso
     */
    public function save(array $data) {
        $id = $this->insert($data);
        if(!is_string($id) and is_int($id) and $id > 0 ) {
            return $id;
        }
        else if(is_string($id)) {
            //esse é o único caso que precisa dar update ?
            //$pos = strpos($id, 'Unique violation');
            
            //if($pos > 0 and $pos !== false) {
                //remove as pks do array de dados para evitar conflito no comando sql
                if(is_array($this->pks)) {
                    foreach($this->pks as $pkey) {
                        $pk[$pkey] = $data[$pkey];
                        unset($data[$pkey]);
                    }
                }
                else {
                    $pk = $data[$this->pks];
                    unset($data[$this->pks]);
                }
                //update
                return $this->update($data, $pk);
            //}
            //return $id;
        }
        return $id;
    }
    
    /**
     * Exclui um campo do banco de dados. Faz a verificação se será uma exclusão lógica ou não
     * 
     * @param  mixed $id Valor do campo ID, se for multiplo um vetor indexado pelo nome das chaves
     * @return int
     */
    public function delete($id) {
        try{
            $where = $this->createWhere($id);
            if (empty($where)) {
                Factory::log()->warn('O campo informado para exclusão é inválido');
                return 0;
            }
            if ($this->hasAtivo()) {
                return $this->db->update(
                    $this->getTableName(), array('ativo' => 'false'), array_merge($where, array('ativo' => true))
                );
            }
            else {
                return $this->db->delete($this->getTableName(), $this->createWhere($id));
            }
        }catch(PDOException $e) {
            Factory::log()->info("[info]error ao deletar na camada DAO : " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Busca todos os dados para o respectivo ID
     * 
     * @param  mixed $id       Valor do campo ID, se for multiplo um vetor indexado pelo nome das chaves
     * @param  bool  $deleted  Determina se irá buscar por registro excluídos lógicamente
     * @return array
     */
    public function getById($id, $deleted=false) {
        $where = $this->createWhere($id);
        if (empty($where)) {
            Factory::log()->warn('Campos informados na consulta são inválidos');
            return array();
        }
        return $this->getByKey(array_keys($where), array_values($where), $deleted);
    }
    
    /**
     * Realiza uma busca utilizando uma chave candidata diferente. 
     * Retorna SEMPRE um ÚNICO valor.
     * 
     * @param  string $key   O nome de uma coluna da tabela
     * @param  mixed  $value O valor buscado/procurado
     * @return array         Retorna uma array associativo que representa uma linha da tabela
     */
    protected function getByKey($key, $value, $deleted=false) {
        if (is_array($key) && is_array($value)) {
            $where = array_combine($key, $value);
        }
        else {
            $where = array($key => $value);
        }
        if ($this->hasAtivo() && !$deleted) {
            $where['ativo'] = true;
        }
        if(empty($this->db)) {
            echo "<center><h3>Estamos fora do ar temporariamente, por favor tente novamente mais tarde!</h3></center>"; exit;
        }
        return $this->db->selectOne('*', $this->getTableName(), $where);
    }
    
    /**
     * Busca os dados da tabela de acordo com a condição
     * 
     * @param mixed        $campos Lista de campos que a consulta deverá retornar
     * @param array        $where  Vetor com as condições
     * @param string|array $order  Campo que será utilizado na ordenação da consulta
     * @param int          $begin  Indice que será inicial a busca
     * @param int          $limit  Máximo de valores que deverá ser retornado
     * @return array               Resultado da consulta
     */
    public function listByCondition($campos, $where, $order=array(), $begin=0, $limit=100) {
        return $this->_listByCondition($this->getTableName(), $campos, $where, $order, $begin, $limit);
    }
    
    /**
     * Busca os dados da tabela de acordo com a condição
     * 
     * @param string        $table O nome de uma tabela ou uma lista de tabelas
     * @param mixed         $campos Lista de campos que a consulta deverá retornar
     * @param array         $where Vetor com as condições
     * @param string|array  $order Campo que será utilizado na ordenação da consulta
     * @param int           $begin Indice que será inicial a busca
     * @param int           $limit Máximo de valores que deverá ser retornado
     * @return array        Retorna o resultado da consulta
     */
    protected function _listByCondition($table, $campos, $where, $order=array(), $begin=0, $limit=100) {
        $extra = array('orderBy' => $order, 'limit' => $limit, 'offset' => $begin);
        if ($this->hasAtivo() && !isset($where['ativo'])) {
            $where[$this->getTableName() . '.ativo'] = true;
        }
        return $this->db->select($campos, $table, $where, $extra);
    }
    
    /**
     * Busca todos os dados da tabela retornando TODAS as linhas correspondentes
     * 
     * @param  string|array $order  Campo que será utilizado na ordenação da consulta
     * @param  int          $begin  Indice que será inicial a busca
     * @param  int          $limit  Máximo de valores que deverá ser retornado
     * @return array                Retorna o resultado da consulta
     */
    public function listAll($order=array(), $begin=0, $limit=100) {
        return $this->listByCondition('*', array(), $order, $begin, $limit);
    }
    
    /**
     * Retorna os dados indexados pela chave primária. Se a chave primária é composta a chave será concatenada
     * 
     * @param mixed        $campos  Um único campo ou uma lista de campos
     * @param string|array $order   Campo que será utilizado na ordenação da consulta
     * @param int          $begin   Indice que será inicial a busca
     * @param int          $limit   Máximo de valores que deverá ser retornado
     * @return array                Resultado da consulta
     */
    public function getIndexedArray($campos='*', $where=array(), $order=array(), $begin=0, $limit=100) {
        return $this->_getIndexedArray($this->getTableName(), $this->getPKFieldName(), $campos, $where, $order, $begin, $limit);
    }
    
    /**
     * Retorna o ultimo id serial
     * 
     * @return int
     */
    public function getLastId() {
        return $this->getDatabase()->lastInsertId($this->getTableName(), $this->getPKFieldName());
    }
    
    /**
     * Retorna os dados indexados pela chave primária. Se a chave primária é composta a chave será concatenada
     * 
     * @param  string        $table   O nome de uma tabela ou uma lista de tabelas
     * @param  mixed         $campos  Um único campo ou uma lista de campos
     * @param  string|array  $order   Campo que será utilizado na ordenação da consulta
     * @param  int           $begin   Indice que será inicial a busca
     * @param  int           $limit   Máximo de valores que deverá ser retornado
     * @return array                  Resultado da consulta
     */
    protected function _getIndexedArray($table, $pks, $campos='*', $where=array(), $order=array(), $begin=0, $limit=100) {
        if (!is_array($campos)) {
            $campos = array($campos);
        }
        $simpleData = count($campos) == 1 && !endsWith($campos[0], '*') && strpos($campos[0], ',') === false;
        if (is_array($pks)) {
            $campos[] = implode("||'_'||", $pks) . ' as _pkid';
        }
        else {
            $campos[] = $pks . ' as _pkid';
        }
        $result = array();
        $data = $this->_listByCondition($table, $campos, $where, $order, $begin, $limit);
        if ($simpleData) {
            //É um campo simples no formato tabela.campo removendo o 'tabela.'
            if (strpos($campos[0], '.') !== false) {
                $campos[0] = substr($campos[0], strrpos($campos[0], '.') + 1);
            }
        }
        foreach ($data as $row) {
            if ($simpleData) {
                $result[$row['_pkid']] = $row[$campos[0]];
            }
            else {
                $key = $row['_pkid'];
                unset($row['_pkid']);
                $result[$key] = $row;
            }
        }
        return $result;
    }
    
    /**
     * Função genérica que retorna o ID de um campo. 
     * Funciona apenas para tabelas com chave primária inteiro e única
     *
     * @param string    $campo  Nome do campo 
     * @param string|id $valor  Id ou valor do $campo que deve ser um campo da tabela
     * @return int              Valor do Id ou -1 caso não encontre o campo
     */
    protected function getTableId($campo, $valor) {
        if (is_numeric($valor)) {
            //Se o valor já é um id retorna-o
            return $valor;
        }
        
        //Busca pelo id do campo em questão
        $result = $this->getByKey($campo, $valor);
        if (empty($result)) {
            Factory::log()->info('O campo "' . $campo . '" não possui o valor "' . $valor . '" para a tabela: ' . $this->getTableName());
            return -1;
        }
        return $result[$this->pks];
    }
    
    /**
     * Carrega um DAO único
     * 
     * @param string $dao Nome do DAO
     */
    protected function loadDAO($dao) {
        $dao = ucfirst(str_replace('DAO', '', $dao));
        if (!isset($this->daos[$dao])) {
            $this->daos[$dao] = Factory::DAO($dao, $this->getDatabase());
        }
        return $this->daos[$dao];
    }
    
    /**
     * 
     * @return string
     */
    public function lastQueryString() {
        return $this->getDatabase()->getStatement()->queryString;
    }
}
