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
    
    /**
     * Retorna a quantidade de dias que faltam para vencer o prazo.<br>
     * Faze-se o seguinte cálculo: prazo - hoje
     * 
     * NOTE: se já estiver fora de prazo o retorno são os dias em número negativo
     * 
     * @param  string|\DateTime $prazo           A data a ser usada na subtração
     * @param  boolean          $returnNegative  se true retorna os dias passados em dias negativos
     * @return int                               A quantidade de dias que faltam  ou zero ou números negativos caso o segundo parametro seja true    
     */
    public function diffFromToday($prazo, $returnNegative = false) {
        if(is_string($prazo)) {
            $prazo = new DateTime($prazo);
        }
        $hoje  = new DateTime();
        
        $dateinterval = $hoje->diff($prazo);/*@var $dateinterval \DateInterval*/
        
        if($hoje > $prazo) {
            if($returnNegative) {
                return $dateinterval->days * -1;
            }
            return 0;
        }
        
        return $dateinterval->days;
    }
}
