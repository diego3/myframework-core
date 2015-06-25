<?php

namespace MyFrameWork\Security;

/**
 *
 * @author Diego Rosa dos Santos <diegosantos@alphaeditora.com.br>
 */
interface Cryptographer {
    const MD5    = "Md5Cryptographer";
    const BCRYPT = "BCryptCryptographer";
    const PBKDF2 = "Pbkdf2Cryptographer";
    
    public function encriptyPassword($password);
    
    public function verifyPassword($password, $hash);
}
