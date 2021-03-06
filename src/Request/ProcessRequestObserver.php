<?php

namespace MyFrameWork\Request;

use MyFrameWork\Event\PublisherInterface;
use MyFrameWork\Event\SubscriberInterface;
use MyFrameWork\Event\ParameterEvent;
use MyFrameWork\Event\EventManager;

/**
 * Description of ProcessRequestObserver
 *
 * @author Diego Rosa dos Santos <diegosantos@alphaeditora.com.br>
 */
abstract class ProcessRequestObserver extends ProcessRequest implements PublisherInterface, SubscriberInterface {
    /**
     *
     * @var \MyFrameWork\Event\EventManager 
     */
    protected $em;
    
    public function setEventManager(EventManager $em) {
        $this->em = $em;
        $this->em->addSubscriber($this);
        return $this;
    }
    
    /**
     * 
     * @return \MyFrameWork\Event\EventManager 
     */
    public function getDispatcher() {
        return $this->em;
    }
    
    //@todo esta ouvindo a si mesmo, e se outro page tambem quiser se registrar ?
    public function getSubscribedEvents() {
        return [
            //"ConstantEventName" => "callbackMethod"
        ];
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
