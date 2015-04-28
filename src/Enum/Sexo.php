<?php

namespace MyFrameWork\Enum;

use MyFrameWork\Enum\BasicEnum;

class Sexo extends BasicEnum {
    const MASCULINO = 'M';
    const FEMININO = 'F';
    
    public function labels() {
        return array(
            self::MASCULINO => 'Masculino',
            self::FEMININO => 'Feminino'
        );
    }
}