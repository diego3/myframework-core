<?php

namespace MyFrameWork\Enum;

use MyFrameWork\Enum\BasicEnum;

/* 
 * Define o tipo da resposta
 */
abstract class ResponseType extends BasicEnum {
    const HTML = 'HTML';
    const JSON = 'JSON';
    const XML  = 'XML';
    const XLS  = 'XLS';
    const CSV  = 'CSV';
    const EMPT = 'EMPTY';
    
    /**
     * 
     * @param type $value
     * @return string
     */
    public static function getDefaultType($value='') {
        return self::isValid($value) ? strtoupper($value) : self::HTML;
    }
}
