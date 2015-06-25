<?php

namespace MyFrameWork\Security;

use Zend\Math\Rand;
use Zend\Crypt\Key\Derivation\Pbkdf2;

use MyFrameWork\Security\Cryptographer;

/**
 * Description of Pbkdf2Cryptographer
 *
 * @author Diego Rosa dos Santos<diegosantos@alphaeditora.com.br>
 */
class Pbkdf2Cryptographer implements Cryptographer{
    
    /**
     * 
     * @return string
     */
    public function createSalt() {
        return  Rand::getBytes(8, true);
    }
    
    public function encriptyPassword($password) {
        return base64_encode( Pbkdf2::calc('sha256', $password, $this->createSalt(), 10000, strlen($password * 2)));
    }

    public function verifyPassword($password, $hash) {
        
    }
}
