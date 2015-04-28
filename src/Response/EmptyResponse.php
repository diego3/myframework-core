<?php

namespace MyFrameWork\Response;

/* 
 * Classe response vazia
 */
class EmptyResponse implements Response {
    
    public function renderContent($content, $file='') {
        
    }
    
    public function setHeader() {
        
    }
}

