<?php

namespace MyFrameWork\Common;
 
//use Zend\Filter\FilterInterface;
 
/**
 * Remove acentuação de uma string
 * 
 * @author Douglas.Pasqua
 */
class RemoveAccent {
    
    /**
     * 
     * @param  string $value  A string na qual vc quer que os acentos sejam removidos
     * @return string         Retorna a string sem os caracteres de acentuação
     */
    public function filter($value) {   
        $from = "áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ";
        $to = "aaaaeeiooouucAAAAEEIOOOUUC";
                 
        $keys = array();
        $values = array();
        
        preg_match_all('/./u', $from, $keys);
        preg_match_all('/./u', $to, $values);
        
        $mapping = array_combine($keys[0], $values[0]);
        $value = strtr($value, $mapping);
        return $value;
    }
}