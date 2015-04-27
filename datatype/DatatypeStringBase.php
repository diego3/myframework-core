<?php

require_once PATH_MYFRAME . '/datatype/Datatype.php';
require_once PATH_LOCAL . '/vendor/Validation/main.php';

use Respect\Validation\Validator as v;

/* 
 * Define o tipo de dado primitivo String
 */
class DatatypeStringBase extends Datatype {
    /**
     * Verifica se o valor é vazio
     * @param string $value
     * @return boolean
     */
    public function isEmpty($value) {
        return strlen($this->valueOf($value)) == 0;
    }
    
    /**
     * Processa o valor retornando-o no tipo base
     * @param mixed $value
     * @return string
     */
    public function valueOf($value) {
        if (is_bool($value)) {
            return var_export($value, true);
        }
        return (string)$value;
    }
    
    /**
     * Converte as tags e caracteres especiais para entities
     * @param string $value
     * @return string
     */
    public function encodetags($value) {
        return htmlentities($value, ENT_COMPAT | ENT_HTML401 | ENT_QUOTES);
    }
    
    //TODO validEncodetags
    
    /**
     * Remove os espaços no inicio e final da string
     * @param string $value
     * @return string
     */
    protected function trim($value) {
        return trim($value);
    }
    
    /**
     * Verifica se o valor não possui espaços vazios no início ou final
     * @param string $value
     * @return boolean
     */
    protected function validTrim($value) {
        if (startsWith($value, ' ') || endsWith($value, ' ')) {
            Factory::log()->warn('Valor possui espaços no início ou no final');
            return false;
        }
        return true;
    }
    
    /**
     * Converte os caracteres da string para minúsculo
     * @param string $value
     * @return string
     */
    protected function lowercase($value) {
        return mb_strtolower($value, 'UTF-8');
    }
    
    /**
     * Verifica se o valor está todo em minúsculo
     * @param string $value
     * @return boolean
     */
    protected function validLowercase($value) {
        if (!v::string()->lowercase()->validate($value)) {
            Factory::log()->warn('Valor deve estar em minúscula');
            return false;
        }
        return true;
    }
    
    /**
     * Converte os caracteres da string para maiúsculo
     * @param string $value
     * @return string
     */
    protected function uppercase($value) {
        return mb_strtoupper($value, 'UTF-8');
    }
    
    /**
     * Verifica se o valor está todo em maiúsculo
     * @param string $value
     * @return boolean
     */
    protected function validUppercase($value) {
        if (!v::string()->uppercase()->validate($value)) {
            Factory::log()->warn('Valor deve estar em minúsculo');
            return false;
        }
        return true;
    }
    
    /**
     * Limpa a string deixando apenas letras, números e espaço
     * @param type $value
     * @return string
     */
    public function alnum($value) {
        return preg_replace('/[^a-zA-Z0-9 ]/', '', $value);
    }
    
    /**
     * Verifica se o valor possui apenas caracteres de a-Z, 0-9 e espaços
     * @param string $value
     * @param array $params Lista de flags
     * @return boolean
     */
    public function validAlnum($value, $params=array()) {
        if (!v::alnum()->validate($value)) {
            if (getValueFromArray($params, Flag::NOWHITESPACE, false)) {
                Factory::log()->warn('Valor deve possuir apenas letras e números');
            }
            else {
                Factory::log()->warn('Valor deve possuir apenas letras, números e espaços');
            }
            return false;
        }
        return true;
    }
    
    /**
     * Limpa a string deixando apenas letras e espaço
     * @param type $value
     * @return string
     */
    public function alpha($value) {
        return preg_replace('/[^a-zA-Z ]/', '', $value);
    }
    
    /**
     * Verifica se o valor possui apenas caracteres de a-Z e espaços
     * @param string $value
     * @param array $params Lista de flags
     * @return boolean
     */
    public function validAlpha($value, $params=array()) {
        if (!v::alpha()->validate($value)) {
            if (getValueFromArray($params, Flag::NOWHITESPACE, false)) {
                Factory::log()->warn('Valor deve possuir apenas letras');
            }
            else {
                Factory::log()->warn('Valor deve possuir apenas letras, números e espaços');
            }
            return false;
        }
        return true;
    }
    
    /**
     * Limpa a string deixando apenas as consoantes e espaços
     * @param type $value
     * @return string
     */
    public function consonant($value) {
        return preg_replace('/[^bcdfghjklmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ ]/', '', $value);
    }
    
    /**
     * Verifica se o valor possui apenas caracteres consoantes e espaços
     * @param string $value
     * @return boolean
     */
    public function validConsonant($value) {
        if (!v::consonant()->validate($value)) {
            Factory::log()->warn('Valor deve possuir apenas consoantes');
            return false;
        }
        return true;
    }
    
    /**
     * Limpa a string deixando apenas números e espaço
     * @param type $value
     * @return string
     */
    public function digit($value) {
        return preg_replace('/[^0-9 ]/', '', $value);
    }
    
    /**
     * Verifica se o valor possui apenas caracteres numéricos
     * @param string $value
     * @param array $params Lista de flags
     * @return boolean
     */
    public function validDigit($value, $params=array()) {
        if (!v::digit()->validate($value)) {
            if (getValueFromArray($params, Flag::NOWHITESPACE, false)) {
                Factory::log()->warn('Valor deve possuir apenas números');
            }
            else {
                Factory::log()->warn('Valor deve possuir apenas números e espaços');
            }
            return false;
        }
        return true;
    }
    
    /**
     * Verifica se o valor possui apenas caracteres imprimiveis ou espaços
     * @param string $value
     * @return boolean
     */
    public function validPrnt($value) {
        if (!v::prnt()->validate($value)) {
            Factory::log()->warn('Valor deve possuir apenas caracteres imprimiveis');
            return false;
        }
        return true;
    }
    
    //TODO public function prnt($value)
    
    /**
     * Remove os espaços, quebras de linha e tabs do texto
     * @param string $value
     * @return string
     */
    public function nowhitespaces($value) {
        return str_replace(array(' ', "\n", "\r", "\t"), '', $value);
    }
    
    /**
     * Verifica se o valor não possui espaços, quebras de linha e tabs
     * @param string $value
     * @return boolean
     */
    public function validNowhitespace($value) {
        if (!v::noWhitespace()->validate($value)) {
            Factory::log()->warn('Valor não pode possuir espaços e quebras de linha');
            return false;
        }
        return true;
    }
    
    /**
     * Verifica se o valor possui apenas caracteres de simbolos (não aceita letras e números)
     * @param string $value
     * @return boolean
     */
    public function validPunct($value) {
        if (!v::punct()->validate($value)) {
            Factory::log()->warn('Valor deve possuir apenas caracteres de pontuação');
            return false;
        }
        return true;
    }
    
    /**
     * Limpa a string deixando apenas vogais
     * @param type $value
     * @return type
     */
    public function vowel($value) {
        return preg_replace('/[^aeiouAEIOU ]/', '', $value);
    }
    
    /**
     * Verifica se o valor possui apenas vogais
     * @param string $value
     * @return boolean
     */
    public function validVowel($value) {
        if (!v::vowel()->validate($value)) {
            Factory::log()->warn('Valor deve possuir apenas vogais');
            return false;
        }
        return true;
    }
    
    /**
     * Limpa a string deixando apenas caracteres hexadecimal
     * @param type $value
     * @return type
     */
    public function xdigit($value) {
        return preg_replace('/[^a-fA-F0-9]/', '', $value);
    }
    
    /**
     * Verifica se o valor possui apenas caracteres hexadecimal
     * @param string $value
     * @return boolean
     */
    public function validXdigit($value) {
        if (!v::xdigit()->validate($value)) {
            Factory::log()->warn('Valor deve possuir apenas caracteres hexadecimal');
            return false;
        }
        return true;
    }
    
    /**
     * Verifica se a quantidade de caracteres minímo é válida
     * @return boolean
     */
    public function validMinlenght($value, $params) {
        $min = getValueFromArray($params, Flag::MINLENGHT, 0);        
        if (!v::string()->length($min, null)->validate($value) && !$this->isEmpty($value)) {            
            Factory::log()->warn('Valor possui menos do que "' . $min . '" caractere(s)');
            return false;
        }
        return true;
    }
    
    /**
     * Verifica se a quantidade de caracteres minímo é válida
     * @return boolean
     */
    public function validMaxlenght($value, $params) {
        $max = getValueFromArray($params, Flag::MAXLENGHT, strlen($value));
        if (!v::string()->length(null, $max)->validate($value)) {
            Factory::log()->warn('Valor possui mais do que "' . $max . '" caractere(s)');
            return false;
        }
        return true;
    }
    
    /**
     * Trunca o valor do texto utilizando o valor disponível na Flag::MAXLENGHT
     * @param string $value
     * @param array $params List of flags
     * @return string
     */
    protected function truncate($value, $params) {
        //Teste para evitar efeito colateral se o trim foi informado após o truncate
        if (getValueFromArray($params, Flag::TRIM, false)) {
            $value = $this->trim($value);
        }
        return substr($value, 0, getValueFromArray($params, Flag::MAXLENGHT, strlen($value)));
    }
}

