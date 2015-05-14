<?php

namespace MyFrameWork\Commonl;

use Zend\Math\Rand;
use Zend\Crypt\Key\Derivation\Pbkdf2;
use Zend\Crypt\Password\Bcrypt;

/**
 * Description of Cryptographer
 *
 * @author Diego
 */
class Cryptographer {
    
    /**
     * 
     * @return string
     */
    public static function createSalt() {
        return  Rand::getBytes(8, true);
    }
    
    /**
     * 
     * @param mixed $password
     * @return string
     */
    public static function encriptyPbkdf2($password) {
        return base64_encode( Pbkdf2::calc('sha256', $password, self::createSalt(), 10000, strlen($password * 2)));
    }
    
    /**
     * criptografa uma senha usando Bcrypt com auxilio da Zend-Framework
     * @note                    requires PHP >= 5.3.23
     * @param string $password  A senha a ser criptografada usando bcrypt
     * @return string           Retorna o hash que deve ser armazenado para verificar no momento da autenticação
     */
    public static function encriptyPassword($password) {
        $bcrypter = new Bcrypt();
        return $bcrypter->create($password);
    }
    
    /**
     * 
     * @param string $password A senha em formato humano
     * @note                   requires PHP >= 5.3.23
     * @param string $hash     O hash gerado para a senha no momento da sua criação
     * @return bool            True se estiver ok ou false caso contrário 
     */
    public static function verifyPassword($password, $hash) {
        $bcrypter = new Bcrypt();
        //$hash = null;//@todo pegar o hash no banco de dados
        return $bcrypter->verify($password, $hash);
    }
    
    
}
//@reference how do you use bcrypt for hashing passwords in PHP ? stackoverflow.com