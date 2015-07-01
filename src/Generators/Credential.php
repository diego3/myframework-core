<?php

namespace MyFrameWork\Generators;

/**
 * Description of CredentialsGenerator
 * 
 * Classe utilitária para criação de usuários e senhas randômicas.
 * As credenciais geradas não são criptografadas.
 * 
 * @author Diego Rosa dos Santos <diegosantos@alphaeditora.com.br>
 */
class Credential {
    
    protected static $alphanum = "abcdefghijklmnopqrstuvxz12345890";
    
    /**
     * Gera um nome de usuário baseado no nome de um arquivo
     * 
     * @param string $fileName O nome de algum arquivo
     * @return string     
     */
    public static function createUserName($fileName) {
        //e se conviter caracteres de acentuação ?? 
        // @see MyFrameWork\Common\RemoveAccent and test
        return substr(str_replace(" ","",$fileName), 0, -4);
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
            $pass .= static::$alphanum[rand(0, 32)];
        }
        return $pass;
    }
    
}
