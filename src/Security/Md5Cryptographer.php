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

    public function verifyPassword($password, $hash) {
        $p = md5($password . $this->salt);
        //dump($p); dump($hash);
        if($p == $hash) {
            return true;
        }
        return false;
    }

}
