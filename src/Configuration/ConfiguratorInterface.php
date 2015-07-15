<?php

namespace MyFrameWork\Configuration;

/**
 * 
 * Consumir um arquivo de configuração 
 */
interface ConfiguratorInterface {
   
    public function isAssociativeArray();
    
    /**
     * Garantir que o retorno é um array associativo 
     */
    public function fileToArray();
    
    public function consumeFile($filePath);
    
}


