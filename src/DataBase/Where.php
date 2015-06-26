<?php
namespace MyFrameWork\DataBase;

/*
 * Classe que contem regras para a criação de uma clausura WHERE ou HAVING
 * As condições serão formadas por vetor de vetores
 * Toda vez que um novo vetor é encontrado o valor do operador é modificado
 * Não suporta subconsultas *por enquanto*
 * 
 * Modo simples (utiliza operadores = e IN):
 *    1. ['campo1' => ['v1', 'v2']]
 *       WHERE campo1 IN ('v1', 'v2')
 *    2. ['campo1' => 'v1', ['campo2' => 'v2', 'campo3' => 'v3']]
 *       WHERE campo1 = 'v1' and (campo2 = 'v2' or campo3 = 'v3')
 *    3. [['campo1' => 'v1', 'campo2' => 'v2']]
 *       WHERE campo1 = 'v1' OR campo2 = 'v2'
 *    4. [['campo1' => 2, ['campo3'=>1, 'campo2'=>2]]
 *       WHERE campo1 = 2 or (campo3=1 and campo2 = 2)
 *
 * Modo verboso:
 *    1. [['attribute' => 'campo1', 'operator' => 'IN', 'value' => ['v1', 'v2']]]
 *       WHERE campo1 IN ('v1', 'v2')
 *    2. [
 *          ['attribute' => 'campo1', 'operator' => 'IN', 'value' => 'v1']],
 *          [
 *              ['attribute' => 'campo2', 'operator' => '=', 'value' => 'v2'],
 *              ['attribute' => 'campo3', 'operator' => '=', 'value' => 'v3']
 *          ]
 *       ]
 *       WHERE campo1 = 'v1' and (campo2 = 'v2' or campo3 = 'v3')
 *    3. [
 *          ['attribute' => 'campo1', 'operator' => '<', 'value' => 6],
 *          ['attribute' => 'campo2', 'operator' => '!=', 'value' => 'v3']
 *       ]
 *       WHERE campo1 < 6 OR campo2 != 'v3'
 *    4. [
 *          [
 *              ['attribute' => 'campo1', 'operator' => '=', 'value' => 2],
 *              [
 *                  ['attribute' => 'campo3', 'operator' => '=', 'value' => 1],
 *                  ['attribute' => 'campo2', 'operator' => '=', 'value' => 2]
 *              ]
 *          ]
 *       ]
 *       WHERE campo1 = 2 or (campo3=1 and campo2 = 2)
 * 
 */
class Where {
    /**
     * 
     * @var type 
     */
    protected $sql = '';
    protected $params = array();
    
    /**
     * Criar o objeto Where
     * @param array $conditions Array no formato wherearray
     */
    public function __construct($conditions) {
        $this->sql = trim($this->proccessSQL($conditions));
    }
    
    /**
     * Realiza a conversão do wherearray para o comando SQL e gera os parâmetros
     * @param array $conditions Array no formato wherearray
     * @param string $glue Operador de junção AND ou OR
     * @return string SQL gerado
     */
    protected function proccessSQL($conditions, $glue='AND') {
        $sql = array();
        if (!is_array($conditions)) {
            return '';
        }
        foreach ($conditions as $key => $value) {
            if (is_numeric($key)) {
                if (is_array($value) && array_key_exists('attribute', $value) && array_key_exists('value', $value) && array_key_exists('operator', $value)) {
                    //Complex format
                    $sql[] = "{$value['attribute']} {$value['operator']} ?";
                    $this->params[] = $value['value'];
                }
                else {
                    //Invert operator
                    $q = $this->proccessSQL($value, $glue == 'AND' ? 'OR' : 'AND');
                    if (!empty($q)) {
                        $sql[] = '(' . $q . ')';
                    }
                }
            }
            else {
                //Simple format
                if (is_array($value)) {
                    $operator = 'IN';
                }
                else {
                    $operator = '=';
                }
                $sql[] = "{$key} {$operator} ?";
                $this->params[] = $value;
            }
        }
        return implode(' ' . $glue . ' ', $sql);
    }
    
    /**
     * Retorna o SQL que deverá ser utilizado nas clausuras WHERE ou HAVING
     * @return string
     */
    public function getSQL() {
        if (empty($this->sql)) {
            return '';
        }
        else {
            return 'WHERE ' . $this->sql;
        }
    }
    
    /**
     * Retorna a lista de parâmetros informados pelo usuário
     * @return array
     */
    public function getParams() {
        return $this->params;
    }
    
    /**
     * Retorna se a condição é vazia ou não
     * @return boolean
     */
    public function isEmpty() {
       return empty($this->sql); 
    }
    
    /**
     * Recebe um parametro where e retorna-o se for válido, se não for válido tenta criar um válido
     * @param mixed $where
     * @return \Where
     */
    public static function getInstance($where) {
        if (is_array($where)) {
            return new Where($where);
        }
        else if (!is_a($where, 'MyFrameWork\\DataBase\\Where')) {
            return new Where(array());
        }
        return $where;
    }
    
    /**
     * Formata uma condição
     * @return array Vetor no formato complexo
     */
    public static function cond($attribute, $operator, $value) {
        return array('attribute' => $attribute, 'operator' => $operator, 'value' => $value);
    }
    
    /**
     * Formata uma única condição
     * @return array Vetor com um vetor no formato complexo
     */
    public static function one($attribute, $operator, $value) {
        return array(self::cond($attribute, $operator, $value));
    }
}
