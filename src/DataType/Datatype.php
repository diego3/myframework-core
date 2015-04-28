<?php
/**
 * Classe base para os tipos de dados manipulados no sistema
 * Os tipos de dados podem ser primitivos ou complexos
 */
abstract class Datatype {
    /**
     * Lista de parâmetros padrão para o tipo 
     * Tais valores são adicionados ou sobrescritos aos parâmetros recebido
     * @var array 
     */
    protected $defaultParams = array();
    
    /**
     * Implementa a lógica de limpeza para o tipo de dado em questão
     * @param mixed $value Valor recebido (normalmente é uma string)
     * @return mixed O valor processado
     */
    protected function _sanitize($value, $params) {
        return $value;
    }
    
    /**
     * Implementa a lógica de verificação que valida o campo.
     * Ele é chamado pelo método público isValid
     * @return bool
     */
    protected function _isValid($value = null, $params) {
        return true;
    }
    
    /**
     * Implementa a lógica de transformação do dado vindo do banco para o formato humano
     * @param mixed $value Valor que deverá ser transformado
     * @param mixed $params
     * @return string
     */
    protected function _toHumanFormat($value, $params) {
        return $value;
    }
    
    /**
     * Realiza o merge dos parâmetros e normaliza os valores lógicos
     * @param array $params
     */
    protected function normalizeParams($params) {
        foreach(array_keys($params) as $key) {
            if (is_numeric($key)) {//quando a chave dos parâmetros será numérica ???
                $params[strval($params[$key])] = true;//o valor do índice numérico passa a ser a chave
                unset($params[$key]);//remove o índice numérico
            }
        }
        return array_merge($params, $this->defaultParams);
    }
    
    /**
     * Recebe o dado no formato humano e realia a limpeza e processamento do mesmo
     * @param mixed $value Valor recebido (normalmente é uma string)
     * @param array $params Lista de parâmetros utilizados no processamento do valor
     * @return mixed O valor processado
     */
    public final function sanitize($value, $params = array()) {
        $params = $this->normalizeParams($params);
        
        //Call internal _sanitize method
        $value = $this->valueOf($value);
        if ($this->isEmpty($value)) {
            $value = $this->valueOf(getValueFromArray($params, Flag::DEFAULT_VALUE, $value));
        }
        $value = $this->_sanitize($value, $params);
        
        //Call sanitizers methods
        foreach ($params as $method => $param) {
            if ($this->isEmpty($value)) {
                break;
            }
            if ($param !== false && method_exists($this, $method)) {
                $value = call_user_func_array(array($this, $method), array($value, $params)); 
            }
        }
        return $value;
    }
    
    /**
     * Determina se o valor informado é válido ou não para o tipo
     * @param mixed $value Valor que deverá ser validado (pode ou não ser o valor limpo)
     * @param array $params Lista de parâmetros utilizados no processamento do valor
     * @return bool
     */
    public final function isValid($value, $params=array()) {
        $params = $this->normalizeParams($params);
        foreach ($params as $validator => $val) {
            $method = 'valid' . ucfirst($validator);
            if ($val !== false && method_exists($this, $method)) {
                if (!call_user_func_array(array($this, $method), array($value, $params))) {
                    return false;
                }
            }
        }
        return $this->_isValid($value, $params);
    }
    
    /**
     * Verifica se o valor é obrigatório e não é vazio
     * @return boolean
     */
    protected function validRequired($value) {
        if ($this->isEmpty($value)) {
            Factory::log()->warn('Valor obrigatório');
            return false;
        }
        return true;
    }
    
    /**
     * Transforma o valor em um formato para humanos
     * @param mixed $value Valor que deverá ser transformado
     * @param array $params Lista de parâmetros utilizados no processamento do valor
     * @return string
     */
    public final function toHumanFormat($value, $params=array()) {
        $params = $this->normalizeParams($params);
        $value = $this->_toHumanFormat($value, $params);
        
        //Aplica a máscara se necessário
        return $this->getValueFromMask($value, getValueFromArray($params, Flag::MASK));
    }

    /**
     * Verifica se o valor é vazio.
     * Para alguns tipos (int e bool) a função empty não retorna como o esperado
     * 
     * @param mixed $value Valor verificado
     * @return bool
     */
    public function isEmpty($value) {
        return is_null($value);
    }
    
     /**
     * Processa o valor retornando-o no tipo base
     * @param mixed $value
     * @return mixed
     */
    public function valueOf($value) {
        return $value;
    }
    
    /**
     * Retorna o código HTML do elemento editável
     * @param string $name
     * @param string $value
     * @param array $params
     * @param array $attr
     * @return string
     */
    public function getHTMLEditable($name, $value, $params, $attr=array()) {
        $params = $this->normalizeParams($params);
        if (empty($value)) {
            $attr['value'] = getValueFromArray($params, Flag::DEFAULT_VALUE, '');
        }
        else {
            $attr['value'] = $value;
        }
        $attr = $this->getHTMLAttributes($attr, $params);
        return HTML::input($name, $attr, $name . '_id');
    }
    
    /**
     * 
     * @param array $attr
     * @param array $params
     * @return array
     */
    protected function getHTMLAttributes($attr, $params) {
        if (empty($attr['class'])) {
            $attr['class'] = '';
        }
        $attr['class'] .= ' form-control';
        $attr['placeholder'] = getValueFromArray($params, Flag::PLACEHOLHER, '');
        
        if (getValueFromArray($params, Flag::REQUIRED, false)) {
            $attr['required'] = 'required';
        }
        return $attr;
    }
    
    
    
    /**
     * Retorna o primeiro valor não vazio.
     * Se ambos os valores forem vazio, será retornado o segundo valor
     * @param mixed $value
     * @param mixed $default
     * @return mixed
     */
    protected function getFirstValue($value, $default) {
        
    }
    
    /**
     * Se há uma mascara retorna o valor formatado, se não há retorna o próprio valor
     * @param string $value
     * @param string $mask
     * @return string
     */
    protected function getValueFromMask($value, $mask) {
        if (empty($mask)) {
            return $value;
        }
        return sprintf($mask, $value);
    }
}
