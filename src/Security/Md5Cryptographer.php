<?php

namespace MyFrameWork\Security;

use MyFrameWork\Security\Cryptographer;

/**
 * Description of Md5Cryptographer
 *
 * @author Diego Rosa dos Santos<diegosantos@alphaeditora.com.br>
 */
class Md5Cryptographer implements Cryptographer {
    
    protected $salt = ")*$&)#2";
    
    public function encriptyPassword($password) {
        return md5($password . $this->salt);
    }
    
    /**
     * Verifica se o hash é valido para o password fornecido
     * 
     * @param string   $password A senha em formato legivel
     * @param string   $hash     O hash da senha gerado pelo encriptyPassword
     * @return boolean           True se o hash for verdadeiro ou false caso contrário   
     */
    public function verifyPassword($password, $hash) {
        if(null === $password) {
            trigger_error("password é nulo ao verifica-lo . ", E_WARNING);
        }
        
        $p = md5($password . $this->salt);
       
        if($p == $hash) {
            return true;
        }
        return false;
    }

}
