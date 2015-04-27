<?php

require_once "Event.php";

/**
 * Description of ParameterEvent
 * 
 * Se um dos parametros for inválido esse evento irá abortar a requisição e irá fazer um redirect
 * para uma nova requisição com os dados sujos.
 * 
 * @author Diego Rosa dos Santos <diegosantos@alphaeditora.com.br>
 */
class ParameterEvent extends Event {
    
    const PARAMETER_ERROR = 'parameter_error';
    const PARAMETER_SUCCESS = 'parameter_success';
    
    protected $parameters;
    
    public function __construct(array $parameters) {
        $this->parameters = $parameters;
    }
    
    public function getParameters() {
        return $this->parameters;    
    }
    
    /**
     * Use esse método somente se esviver usando o PageEventManager
     * @return array
     */
    public function getInvalidParameters() {
        if(isset($_SESSION["parametersMeta"])) {
            $params = $_SESSION["parametersMeta"];
            unset($_SESSION["parametersMeta"]);
            return $params;
        }
        return array();
    }
    
}
