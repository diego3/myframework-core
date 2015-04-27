<?php

/* 
 * Define o tipo da resposta
 */
require_once PATH_MYFRAME . '/enum/BasicEnum.php';

abstract class ResponseType extends BasicEnum {
    const HTML = 'HTML';
    const JSON = 'JSON';
    const XML = 'XML';
    const XLS = 'XLS';
    const CSV = 'CSV';
    /**
     * 
     * @param type $value
     * @return string
     */
    public static function getDefaultType($value='') {
        return self::isValid($value) ? strtoupper($value) : self::HTML;
    }
}
