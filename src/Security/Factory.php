<?php

namespace MyFrameWork\Security;

/**
 * Description of Factory
 *
 * @author Diego Rosa dos Santos<diegosantos@alphaeditora.com.br>
 */
class Factory {
    
    /**
     * 
     * @param string $type SEMPRE USE as constantes da Cryptographer interface
     * @return \MyFrameWork\Security\Cryptographer
     */
    public static function getCryptographer($type) {
        $reflect = new \ReflectionClass("MyFrameWork\\Security\\" . $type);
        
        $instance = $reflect->newInstance();
        return $instance;
    }
}
