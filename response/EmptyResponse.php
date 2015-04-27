<?php
require_once PATH_MYFRAME . '/response/Response.php';

/* 
 * Classe response vazia
 */
class EmptyResponse implements Response {
    
    public function renderContent($content, $file='') {
        
    }
    
    public function setHeader() {
        
    }
}

