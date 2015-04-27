<?php

require_once PATH_MYFRAME . '/Template.php';
require_once PATH_MYFRAME . '/response/HtmlResponse.php';

/* 
 * Classe response para HTML
 */

class XmlResponse extends HtmlResponse {
    public function setHeader() {
        header('Content-Type: text/xml;charset=UTF-8');
    }
}

