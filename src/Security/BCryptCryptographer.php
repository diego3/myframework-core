<?php

namespace MyFrameWork\Security;

use Zend\Crypt\Password\Bcrypt;

use MyFrameWork\Security\Cryptographer;

/**
 * Description of BCryptCryptographer
 *
 * @author Diego Rosa dos Santos <diegosantos@alphaeditora.com.br>
 */
class BCryptCryptographer implements Cryptographer {
    
    /**
     * criptografa uma senha usando Bcrypt com auxilio da Zend-Framework
     * 
     * @note                    requires PHP >= 5.3.23
     * @param string $password  A senha a ser criptografada usando bcrypt
     * @return string           Retorna o hash que deve ser armazenado para verificar no momento da autenticação
     */
    public function encriptyPassword($password) {
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
    public function verifyPassword($password, $hash) {
        $bcrypter = new Bcrypt();
        return $bcrypter->verify($password, $hash);
    }
    
}
//@reference how do you use bcrypt for hashing passwords in PHP ? stackoverflow.com