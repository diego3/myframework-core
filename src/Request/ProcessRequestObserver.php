<?php

require_once PATH_MYFRAME . "/request/ProcessRequest.php";
require_once PATH_MYFRAME . "/event/PublisherInterface.php";
require_once PATH_MYFRAME . "/event/SubscriberInterface.php";

/**
 * Description of ProcessRequestObserver
 *
 * @author Diego Rosa dos Santos <diegosantos@alphaeditora.com.br>
 */
abstract class ProcessRequestObserver extends ProcessRequest implements PublisherInterface, SubscriberInterface {
    /**
     *
     * @var EventManager 
     */
    protected $em;
    
    public function __construct(EventManager $em) {
        $this->em = $em;
        $this->em->addSubscriber($this);
    }
    
    public function getDispatcher() {
        return $this->em;
    }
    
    public function getSubscribedEvents() {
        return array(
            
        );
    }
    
    protected function isValidParameters() {
        $status = parent::isValidParameters();
        if(!$status) {
            $event = new ParameterEvent($this->parametersMeta);
            $this->getDispatcher()->dispatch(ParameterEvent::PARAMETER_ERROR, $event);
        }
        //TODO disparar o PARAMETER_SUCCESS
        return $status;
    }
    
    protected function isParametersValid() {
        $status = parent::isParametersValid();
        if(!$status) {
            $event = new ParameterEvent($this->parametersMeta);
            $this->getDispatcher()->dispatch(ParameterEvent::PARAMETER_ERROR, $event);
        }
        //TODO disparar o PARAMETER_SUCCESS
        return $status;
    }

}
