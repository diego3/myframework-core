<?php

require_once PATH_MYFRAME . '/Template.php';
require_once PATH_MYFRAME . '/response/HtmlResponse.php';

/* 
 * Classe response para HTML
 */
class TxtResponse extends HtmlResponse {
    public function setHeader() {
        header('Content-Type: text/plain;charset=UTF-8');
    }
}

