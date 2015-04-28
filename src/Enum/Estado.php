<?php

require_once PATH_MYFRAME . '/enum/BasicEnum.php';

class Estado extends BasicEnum {
    const ACRE = 'AC';
    const ALAGOAS = 'AL';
    const AMAPA = 'AP';
    const AMAZONAS = 'AM';
    const BAHIA = 'BA';
    const CEARA = 'CE';
    const DISTRITO_FEDERAL = 'DF';
    const ESPIRITO_SANTO = 'ES';
    const GOIAS = 'GO';
    const MARANHAO = 'MA';
    const MATO_GROSSO = 'MT';
    const MATO_GROSSO_DO_SUL = 'MS';
    const MINAS_GERAIS = 'MG';
    const PARA = 'PA';
    const PARAIBA = 'PB';
    const PARANA = 'PR';
    const PERNAMBUCO = 'PE';
    const PIAUI = 'PI';
    const RIO_DE_JANEIRO = 'RJ';
    const RIO_GRANDE_DO_NORTE = 'RN';
    const RIO_GRANDE_DO_SUL = 'RS';
    const RONDONIA = 'RO';
    const RORAIMA = 'RR';
    const SANTA_CATARINA = 'SC';
    const SAO_PAULO = 'SP';
    const SERGIPE = 'SE';
    const TOCANTINS = 'TO';
    
    public function labels() {
        return array(
            self::ACRE => 'Acre',
            self::ALAGOAS => 'Alagoas',
            self::AMAPA => 'Amapá',
            self::AMAZONAS => 'Amazonas',
            self::BAHIA => 'Bahia',
            self::CEARA => 'Ceará',
            self::DISTRITO_FEDERAL => 'Distrito Federal',
            self::ESPIRITO_SANTO => 'Espírito Santo',
            self::GOIAS => 'Goiás',
            self::MARANHAO => 'Maranhão',
            self::MATO_GROSSO => 'Mato Grosso',
            self::MATO_GROSSO_DO_SUL => 'Mato Grosso do Sul',
            self::MINAS_GERAIS => 'Minas Gerais',
            self::PARA => 'Pará',
            self::PARAIBA => 'Paraíba',
            self::PARANA => 'Paraná',
            self::PERNAMBUCO => 'Pernambuco',
            self::PIAUI => 'Piauí',
            self::RIO_DE_JANEIRO => 'Rio de Janeiro',
            self::RIO_GRANDE_DO_NORTE => 'Rio Grande do Norte',
            self::RIO_GRANDE_DO_SUL => 'Rio Grande do Sul',
            self::RONDONIA => 'Rondônia',
            self::RORAIMA => 'Roraima',
            self::SANTA_CATARINA => 'Santa Catarina',
            self::SAO_PAULO => 'São Paulo',
            self::SERGIPE => 'Sergipe',
            self::TOCANTINS => 'Tocantins'
        );
    }
}