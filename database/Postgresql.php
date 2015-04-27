<?php

/**
 * Contém as classes de abstração do banco de dados Postgresql
 */
require_once PATH_MYFRAME . '/database/DataBase.php';
require_once PATH_MYFRAME . '/database/Query.php';

/**
 * Trabalha uma conexão com o banco Postgres.
 */
class PgDataBase extends DataBase{
    /**
     * Retorna o DSN específico do Postgres
     * @return string
     */
    protected function getDsn() {
        if (empty($this->port)) {
            $this->port = '5432';
        }
        return "pgsql:host={$this->host};port={$this->port};dbname={$this->dbname}";
    }

    /**
     * Armazena um objeto de consulta
     * @var \Query 
     */
    protected $query;
    
    /**
     * Retorna o objeto de consulta
     * @return \Query
     */
    public function getQuery() {
        if ($this->query == null) {
            $this->query = new PgQuery();
        }
        return $this->query;
    }
    
    /**
     * Retorna o ultimo id inserido
     * @param string $name Nome da tabela
     * @param string $column Nome da coluna
     * @return type
     */
    public function lastInsertId($nameOrTable = null, $column = null) {
        if (empty($column)) {
            return parent::lastInsertId($nameOrTable);
        }
        else {
            return parent::lastInsertId($nameOrTable . '_' . $column . '_seq');
        }
    }
}

class PgQuery extends Query {
}