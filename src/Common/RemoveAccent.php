<?php

namespace MyFrameWork\Common;
 
//use Zend\Filter\FilterInterface;
 
/**
 * Remove acentuação de uma string
 * @author Douglas.Pasqua
 *
 */
class RemoveAccent 
{
    public function filter($value)
    {   
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