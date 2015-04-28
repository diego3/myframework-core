<?php

namespace MyFrameWork\Response;

/* 
 * Classe response para HTML
 */
class TxtResponse extends HtmlResponse {
    public function setHeader() {
        header('Content-Type: text/plain;charset=UTF-8');
    }
}

