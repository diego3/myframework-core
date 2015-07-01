<?php

namespace MyFrameWork\Common;

/**
 * Classe utilitária para trabalhar com diretórios e arquivos do sistema
 *
 * @author Diego Rosa dos Santos <diegosantos@alphaeditora.com.br>
 */
class Directory {
    
    
    /**
     * Verifica se um diretório existe dentro de outro diretório
     * 
     * @param  string  $in     O path no qual será feito a busca
     * @param  string  $target O diretorio a ser procurado dentro de $in
     * @return boolean
     */
    public function directoryExists($in, $target) {
        $folderExists = false;
        
        $directory = new \DirectoryIterator($in);
        
        foreach($directory as $fileInfo) {
            if($fileInfo->isDir() and $fileInfo->getBasename() == $target) {
                $folderExists = true;
                break;
            }
        }
        
        return $folderExists;
    }
    
    
}
