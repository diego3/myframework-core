<?php

namespace MyFrameWork\Response;

/* 
 * Classe response para HTML
 */

class XmlResponse extends HtmlResponse {
    public function setHeader() {
        header('Content-Type: text/xml;charset=UTF-8');
    }
}

