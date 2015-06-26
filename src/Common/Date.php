<?php

namespace MyFrameWork\Common;

use DateTime;

/**
 * Classe utilitária para trabalhar operações envolvendo datas
 *
 * @author Diego Rosa dos Santos <diegosantos@alphaeditora.com.br>
 */
class Date {
    
    /**
     * Diz se já passou ou do prazo a partir da data atual
     * 
     * @param  mixed $prazo string ou DateTime
     * @return boolean
     * @throws Exception caso o $prazo seja uma data invalida
     */
    public function isValid($prazo) {
        if($prazo instanceof DateTime) {
            $dt_prazo = $prazo;
        }
        else if(is_string($prazo)) {
            $dt_prazo = new DateTime($prazo);
        }
        else {
            throw new Exception("prazo invalido para o metodo isValid de Date");
        }
        
        $hoje  = new DateTime();
        
        if($hoje > $dt_prazo) {
            return true;
        }
        return false;
    }
    
    public function diffFromToday($prazo) {
        $dt_prazo = new DateTime($prazo);
        $hoje  = new DateTime();
        
        $dateinterval = $hoje->diff($dt_prazo);/*@var $dateinterval \DateInterval*/
        return $dateinterval->days;
    }
}
