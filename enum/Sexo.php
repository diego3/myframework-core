<?php

require_once PATH_MYFRAME . '/enum/BasicEnum.php';

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