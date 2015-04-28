<?php
namespace MyFrameWork\DataBase;


use MyFrameWork\DataBase\DataBase,
    MyFrameWork\DataBase\MyQuery;

/**
 * 
 * Abstrai a lógica do banco de dados mysql
 */
class MyDataBase extends DataBase {
    /**
     * Retorna o DSN específico do Mysql
     * @return string
     */
    protected function getDsn() {
        if (empty($this->port)) {
            $this->port = '3306';
        }
        return "mysql:host={$this->host};port={$this->port};dbname={$this->dbname}";
    }

    /**
     * Contém um objeto de consulta
     * @var \Query 
     */
    protected $query;
    
    /**
     * Retorna um objeto de consulta
     * @return \Query
     */
    public function getQuery() {
        if ($this->query == null) {
            $this->query = new MyQuery();
        }
        return $this->query;
    }

    /**
     * Retorna o ultimo id inserido
     * @param string $name O valor é ignorado para o banco de dados Mysql
     * @param string $column O valor é ignorado para o banco de dados Mysql
     * @return type
     */
    public function lastInsertId($name = null, $column = null) {        
        return parent::lastInsertId(null);
    }
}