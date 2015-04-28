<?php

namespace MyFrameWork\DataBase;
        
use MyFrameWork\DataBase\Where;
use MyFrameWork\DataBase\Expression;
use MyFrameWork\Factory;

/* 
 * Classe utilitária para criação de consultas SQL
 * Cada banco de dados deve implementar sua própria classe Query
 * 
 * Os comandos geradores de SQL retornam sempre o formato queryresult
 * queryresult é um vetor que possui as seguintes chaves:
 *      status => boolean   // Determina se a query foi bem formada ou não
 *      sql => string       // Comando SQL gerado quando a query foi ber formada
 *      values => array()    // Lista de valores que deverá ser passado para o prepare
 */
abstract class Query {
    /**
     * Retorna um queryresult no formato de erro
     * @param string $msg Mensagem de erro
     * @return array queryresult
     */
    protected function returnQueryResultError($msg) {
        Factory::log()->error($msg);
        return $this->getQueryResult('', array(), false, $msg);
    }
    
    /**
     * Retorna um array no formato queryresult
     * @param string $sql SQL gerado
     * @param array $values lista de valores
     * @param boolean $status Define se o SQL foi gerado corretamente ou não
     * @return array queryresult
     */
    protected function getQueryResult($sql, $values, $status=true, $error='') {
        return array('status' => $status, 'sql' => $sql, 'values' => $values, 'message' => $error);
    }
    
    /**
     * Valida o nome de uma tabela
     * @param string $table
     * @return boolean
     */
    protected function validateTable($table) {
        if (!is_string($table)) {
            return false;
        }
        $table = trim($table);
        return !empty($table) && !is_numeric($table);
    }
    
    /**
     * Valida se a lista de atributos é válida
     * @param array $attrs
     * @return boolean
     */
    protected function validateAttributes($attrs) {
        return !empty($attrs) && is_assoc($attrs);
    }
    
    /**
     * Processa a query de consulta
     * @param Expression $qry
     * @return array No formato queryresult
     */
    protected function proccessQuery(Expression $qry) {
        $params = array();
        $sql = '';
        if ($qry->hasWhere()) {
            $sql = $qry->getWhere()->getSQL() . ' ';
            $params = $qry->getWhere()->getParams();
        }
        
        if ($qry->hasGroupBy()) {
            $sql.= 'GROUP BY ' . implode(',', $qry->getGroupBy()) . ' ';
        }
        
        if ($qry->hasHaving()) {
            $count = 1;
            $sql.= str_replace('WHERE', 'HAVING', $qry->getHaving()->getSQL(), $count) . ' ';
            $params = array_merge($params, $qry->getHaving()->getParams());
        }
        
        if ($qry->hasOrderBy()) {
            $order = array();
            foreach ($qry->getOrderBy() as $cp => $ord) {
                $order[] = $cp . ' ' . $ord;
            }
            $sql.= 'ORDER BY ' . implode(',', $order) . ' ';
        }
        
        if ($qry->hasLimit()) {
            $sql.= 'LIMIT ? ';
            $params[] = $qry->getLimit();
        }
        
        if ($qry->hasOffset() && $qry->getOffset() > 0) {
            $sql.= 'OFFSET ? ';
            $params[] = $qry->getOffset();
        }
        return $this->getQueryResult($sql, $params);
    }
    
    /**
     * Gera um comando SQL de inserção no banco de dados
     * @param string $table Nome da tabela
     * @param array $attrs Array associativo mapeando chave => valor, para atributo => valor
     * @example
     *   var_dump($query->insert("tabela1", ['campo1' => 1, 'campo2' => 2]));
     *   //Saida: array(
     *      'status' => true,
     *      'sql' => 'INSERT INTO tabela1 (campo1, campo2) VALUES (?, ?)',
     *      'values' => array(1, 2)
     *    )
     * 
     * @return array No formato queryresult
     */
    public function insert($table, $attrs) {
        if (!$this->validateTable($table)) {
            return $this->returnQueryResultError('O nome da tabela é inválido');
        }
        if (!$this->validateAttributes($attrs)) {
            return $this->returnQueryResultError('Atributos inválidos');
        }
        
        $values = implode(',', array_fill(0, count($attrs), '?'));
        $sql = 'INSERT INTO ' . $table . ' (' . implode(',', array_keys($attrs)) . ') VALUES (' . $values . ')';
        return $this->getQueryResult($sql, array_values($attrs));
    }
    
    /**
     * Gera um comando SQL de update no banco de dados
     * Se não houver uma condição WHERE por segurança não será gerado o comando
     * @param string $table Nome da tabela
     * @param array $attrs Array associativo mapeando chave => valor, para atributo => valor
     * @param mixed $where Array associativo no formato wherearray ou um objeto Where
     * @example
     *   var_dump($query->update("tabela1", ['campo1' => 1, 'campo2' => 2], ['id' => 33]));
     *   //Saida: array(
     *      'status' => true,
     *      'sql' => 'UPDATE tabela1 SET campo1=?, campo2=? WHERE id=?',
     *      'values' => array(1, 2, 33)
     *    )
     * 
     * @return array No formato queryresult
     */
    public function update($table, $attrs, $where) {
        if (!$this->validateTable($table)) {
            return $this->returnQueryResultError('O nome da tabela é inválido');
        }
        if (!$this->validateAttributes($attrs)) {
            return $this->returnQueryResultError('Atributos inválidos');
        }

        $where = Where::getInstance($where);
        if ($where->isEmpty()) {
            return $this->returnQueryResultError('Condição inválida para um comando UPDATE');
        }

        $sql = 'UPDATE ' . $table . ' SET ' . implode('=?,', array_keys($attrs)) . '=? ' . $where->getSQL();
        return $this->getQueryResult($sql, array_merge(array_values($attrs), $where->getParams()));
    }
    
    /**
     * Gera um comando SQL de delete no banco de dados
     * Se não houver uma condição WHERE por segurança não será gerado o comando
     * @param string $table Nome da tabela
     * @param mixed $where Array associativo no formato wherearray ou um objeto Where
     * @example
     *   var_dump($query->delete("tabela1", ['id' => 33]));
     *   //Saida: array(
     *      'status' => true,
     *      'sql' => 'DELETE FROM tabela1 WHERE id = ?',
     *      'values' => array(33)
     *    )
     * 
     * @return array No formato queryresult
     */
    public function delete($table, $where) {
        if (!$this->validateTable($table)) {
            return $this->returnQueryResultError('O nome da tabela é inválido');
        }
        $where = Where::getInstance($where);
        if ($where->isEmpty()) {
            return $this->returnQueryResultError('Condição inválida para um comando DELETE');
        }
        $sql = 'DELETE FROM ' . $table . ' ' . $where->getSQL();
        return $this->getQueryResult($sql, $where->getParams());
    }

    /**
     * Gera um comando SQL de select no banco de dados
     * @param array $columns Vetor com as colunas utilizadas na consulta
     * @param string $table Nome da tabela
     * @param mixed $where Array associativo no formato wherearray, um objeto Where ou uma string
     * @param mixed $extra Array ou Expression Se $where for uma string $extra será considerado parâmetros
     * @example 
     *   var_dump($query->select(['cp1', 'cp2'], "tabela1", ['where' => ['id' => 33]]));
     *   //Saida: array(
     *      'status' => true,
     *      'sql' => 'SELECT cp1,cp2 FROM tabela1 WHERE id = ?',
     *      'values' => array(33)
     *    )
     * 
     * @return array No formato queryresult
     */
    public function select($columns, $table, $where=array(), $extra=array()) {
        $table = $this->proccessTable($table);
        if (!$this->validateTable($table)) {
            return $this->returnQueryResultError('O nome da tabela é inválido');
        }
        if (empty($columns)) {
            return $this->returnQueryResultError('Deve ser informado pelo menos uma coluna');
        }
        if (!is_array($columns)) {
            $columns = array($columns);
        }
        if (is_string($where) && !empty($where)) {
            $result = array(
              'sql' => $where,
              'values' => $extra
            );
        }
        else {
            if (!empty($where)) {
                if (is_array($extra)) {
                    $extra['where'] = $where;
                }
                else if (is_a($extra, 'Expression')) {
                    $extra->setWhere($where);
                }
            }
            $result = $this->proccessQuery(Expression::getInstance($extra));
        }
        
        $sql = 'SELECT ' . implode(',', $columns) . ' FROM ' . $table . ' ' . $result['sql'];
        return $this->getQueryResult($sql, $result['values']);
    }
    
    /**
     * Realiza o tratamento das tabelas
     * @param array|string $table Nome da tabela ou vetor com as diversas tabelas
     *      Exemplo: array('tabela1' => '', 'tabela2' => 'ON tabela1.codigo = tabela2.codigo')
     *      Resulta em: 'FROM tabela1 INNER JOIN tabela2 ON tabela1.codigo = tabela2.codigo'
     * @return string Formato final das strings
     */
    protected function proccessTable($table) {
        if (is_array($table)) {
            $tables = array_keys($table);
            $joins = array_values($table);
            $table = $tables[0];
            $t = count($tables);
            for ($i=1; $i<$t; $i++) {
                if (!startsWith(trim($joins[$i]), 'ON')) {
                    $joins[$i] = 'ON ' . $joins[$i];
                }
                $table .= ' INNER JOIN ' . $tables[$i] . ' ' . $joins[$i] . ' ';
            }            
        }
        return trim($table);
    }
}