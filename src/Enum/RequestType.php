<?php

namespace MyFrameWork\Enum;

use MyFrameWork\Enum\BasicEnum;

abstract class RequestType extends BasicEnum {
    const GET = 'GET';
    const POST = 'POST';
    
    public static function getInternalInput($type) {
        if ($type == self::GET) {
            return INPUT_GET;
        }
        else {
            return INPUT_POST;
        }
    }
}
