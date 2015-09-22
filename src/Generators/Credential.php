<?php

namespace MyFrameWork\Generators;

use MyFrameWork\Common\RemoveAccent;

/**
 * Description of CredentialsGenerator
 * 
 * Classe utilitária para criação de usuários e senhas randômicas.
 * As credenciais geradas não são criptografadas.
 * 
 * @author Diego Rosa dos Santos <diegosantos@alphaeditora.com.br>
 */
class Credential {
    
    protected static $alphanum = "ABCDEFGHIJKLMNOPQRSTUVXZabcdefghijklmnopqrstuvxz12345890#";
    
    /**
     * Gera um nome de usuário extraindo letras de um texto qualquer
     * 
     * @param  string  $string         O texto no qual o nome será extraído
     * @param  boolean $removeaccents  Se vai manter ou remover os caracteres acentuados
     * @return string     
     */
    public static function createUserName($string, $removeaccents = false) {
        if($removeaccents){
            $string = (new RemoveAccent())->filter($string);
        }
        return strtolower(substr(str_replace(" ","",$string), 0, strpos($string, ' ')+3));
    }
    
    /**
     * Gera uma senha aleatória baseado em caracteres alphanuméricos
     * 
     * @param int $length O tamanho da senha a ser gerada 
     * @return string     Retorna uma senha aleatória
     */
    public static function createPassword($length = 6) {
        $pass = "";
        for ($i = 0; $i < $length; $i++) {
            $pass .= static::$alphanum[rand(0, strlen(static::$alphanum))];
        }
        return $pass;
    }
    
}
