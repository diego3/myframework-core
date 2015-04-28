<?php
require_once PATH_MYFRAME . '/event/PageEventManager.php';
require_once PATH_MYFRAME . '/request/ProcessRequest.php';
require_once PATH_MYFRAME . '/event/PagePublisherInterface.php';

/**
 * Description of ProcessRequestPublisher
 *
 * @author Diego Rosa dos Santos <diegosantos@alphaeditora.com.br>
 */
class ProcessRequestPublisher extends ProcessRequest implements PagePublisherInterface {
    /**
     *
     * @var EventManager
     */
    protected $em;
    
    public function __construct(PageEventManager $em) {
        $this->em = $em;
    }

    public function getDispatcher() {
        return $this->em;
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
