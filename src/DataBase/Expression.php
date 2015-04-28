<?php
namespace MyFrameWork\DataBase;

use MyFrameWork\DataBase\Where;

/* 
 * Classe que representa uma condição de uma consulta SQL a partir do FROM
 */
class Expression {
    /**
     * Condição para filtrar a consulta
     * @var Where 
     */
    protected $where;
    
    /**
     * Lista com os atributos utilizados no agrupamento
     * @var array
     */
    protected $groupBy = array();
    
    /**
     * Condição utilizada para filtrar o agrupamento
     * @var Where
     */
    protected $having;
    
    /**
     * Quantidade de linhas que a consulta deverá retornar
     * @var int
     */
    protected $limit;
    
    /**
     * Ponto de inicio do retorno
     * @var int
     */
    protected $offset;
    
    /**
     * Array associativo com os campos e a ordem que o mesmo deverá ter
     * @var array()
     */
    protected $orderBy = array();
    
    /**
     * Criar um Expression
     * @param array $parts Contém um array com as seguintes chaves
     *      where => wherearray ou um objeto Where para condição da consulta
     *      orderBy => Array no formado order by
     *      limit => Limite
     *      offset => Início
     *      groupBy => Lista de campos para agrupamento
     *      having => wherearray ou um objeto Where para condição do agrupamento
     */
    public function __construct($parts=array()) { 
        $this->setWhere(getValueFromArray($parts, 'where', array()));
        $orderBy = getValueFromArray($parts, 'orderBy', array());
        if (!is_array($orderBy)) {
            $orderBy = array($orderBy);
        }
        foreach ($orderBy as $k => $v) {
            if (is_numeric($k)) {
                $this->addOrderBy($v);
            }
            else {
                $this->addOrderBy($k, $v);
            }
        }
        $this->setGroupBy(getValueFromArray($parts, 'groupBy', array()));
        $this->limit = getValueFromArray($parts, 'limit');
        $this->offset = getValueFromArray($parts, 'offset');
        $this->setHaving(getValueFromArray($parts, 'having', array()));
    }
    
    /**
     * Adiciona uma condição para a clausurá WHERE
     * @param mixed $where wherearray ou um objeto Where para condição da consulta
     * @return \Expression
     */
    public function setWhere($where) {
        $this->where = Where::getInstance($where);
        return $this;
    }
    
    /**
     * Adiciona uma condição para a clausurá HAVING
     * @param mixed $where wherearray ou um objeto Where para condição da consulta
     * @return \Expression
     */
    public function setHaving($where) {
        $this->having = Where::getInstance($where);
        return $this;
    }
    
    /**
     * Define uma lista de atributos no qual a consulta deverá ser agrupada
     * @param array $attr Lista de atributos
     * @return \Expression
     */
    public function setGroupBy($attr) {
        if (is_array($attr)) {
            foreach ($attr as $at) {
                $this->addGroupBy($at);
            }
        }
        else if (is_string($attr)) {
            $this->groupBy = array($attr);
        }
        return $this;
    }
    
    /**
     * Adiciona um atributo a lista dos atributos de agrupamento
     * @param string $attr Nome do atributo
     * @return \Expression
     */
    public function addGroupBy($attr) {
        if (is_string($attr)) {
            $this->groupBy[] = $attr;
        }
        return $this;
    }
    
    /**
     * Define os limites da consulta
     * @param int $limit Quantos valores são desejados no retorno
     * @param int $offset A partir de qual posição
     * @return \Expression
     */
    public function setLimit($limit, $offset=0) {
        if (is_numeric($limit)) {
            $this->limit = $limit;
        }
        if (is_numeric($offset)) {
            $this->offset = $offset;
        }
        return $this;
    }
    
    /**
     * Define a ordem da consulta
     * @param string $column
     * @param string $order ASC ou DESC
     * @return \Expression
     */
    public function addOrderBy($column, $order='ASC') {
        $order = strtoupper($order);
        if ($order == 'ASC' || $order == 'DESC') {
            $this->orderBy[$column] = $order;
        }
        return $this;
    }
    
    /**
     * Zera as configurações de Ordem
     * @return \Expression
     */
    public function clearOrderBy() {
        $this->orderBy = array();
        return $this;
    }
    
    /**
     * Zera as configurações de Agrupamento
     * @return \Expression
     */
    public function clearGroupBy() {
        $this->groupBy = array();
        return $this;
    }
    
    /**
     * Zera as Condições WHERE
     * @return \Expression
     */
    public function clearWhere() {
        $this->where = new Where(array());
        return $this;
    }
    
    /**
     * Zera as Condições de Having
     * @return \Expression
     */
    public function clearHaving() {
        $this->having = new Where(array());
        return $this;
    }
    
    /**
     * Zera as Condições de Having
     * @return \Expression
     */
    public function clearLimits() {
        $this->limit = null;
        $this->offset = null;
        return $this;
    }
    
    /**
     * Retorna se o limite foi definido
     * @return boolean
     */
    public function hasLimit() {
        return is_numeric($this->limit);
    }
    
    /**
     * Retorna se o limite foi definido
     * @return boolean
     */
    public function hasOffset() {
        return is_numeric($this->offset);
    }
    
    /**
     * Retorna se foi definido uma condição para a consulta
     * @return boolean
     */
    public function hasWhere() {
        return !$this->where->isEmpty();
    }
    
    /**
     * Retorna se foi definido uma condição para agrupamento
     * @return boolean
     */
    public function hasHaving() {
        return !$this->having->isEmpty();
    }
    
    /**
     * Retorna se foi definido agrupamento
     * @return boolean
     */
    public function hasGroupBy() {
        return !empty($this->groupBy);
    }
    
    /**
     * Retorna se foi definido agrupamento
     * @return boolean
     */
    public function hasOrderBy() {
        return !empty($this->orderBy);
    }
    
    /**
     * Retorna a condição da consulta
     * @return Where
     */
    public function getWhere() {
        return $this->where;
    }
    
    /**
     * Retorna a condição de agrupamento
     * @return Where
     */
    public function getHaving() {
        return $this->having;
    }
    
    /**
     * Retorna o valor do limite
     * @return int
     */
    public function getLimit() {
        return $this->limit;
    }
    
    /**
     * Retorna o valor do offset
     * @return int
     */
    public function getOffset() {
        return $this->offset;
    }
    
    /**
     * Retorna a lista de valores para ORDER BY
     * @return array
     */
    public function getOrderBy() {
        return $this->orderBy;
    }
    
    /**
     * Retorna a lista de valores para Group BY
     * @return array
     */
    public function getGroupBy() {
        return $this->groupBy;
    }

    /**
     * Recebe um array com os dados para criar um Expression ou um próprio objeto Expression
     * Se for passado um wherearray ou um objeto Where ele será usado como condição inicial da consulta
     * 
     * @param mixed $parts
     * @return \Expression
     */
    public static function getInstance($parts) {
        if (!is_a($parts, 'Expression')) {
            return new Expression($parts);
        }
        return $parts;
    }
}
