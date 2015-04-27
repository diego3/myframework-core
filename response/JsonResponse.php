<?php

require_once PATH_MYFRAME . '/response/Response.php';

/* 
 * Classe response para JSON
 */

class JsonResponse implements Response {
    
    public function setHeader() {
        header('Content-Type: application/json;charset=UTF-8');
        //JSOMP - text/javascript
    }

    public function renderContent($content, $file='') {
        echo json_encode($content);
    }
}

