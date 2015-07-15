<?php

namespace MyFrameWork\Configuration;

use MyFrameWork\Configuration\ConfiguratorInterface;

class IniConfigurator implements ConfiguratorInterface {
    
    protected $file;
    
    /**
     * 
     * @param string $filePath
     * @return array
     */
    public function consumeFile($filePath) {
        return parse_ini_file($filePath, true);
    }

    public function fileToArray() {
        
    }

    public function isAssociativeArray() {
        
    }    
}


