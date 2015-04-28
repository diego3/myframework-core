<?php

namespace MyFrameWork\Event;

use MyFrameWork\Event\ParameterEvent;
use MyFrameWork\Event\SubscriberInterface;

/**
 * Gerencia eventos entre requisições devido a não possibilidade de manter a instância do listener ativa durante o disparo do evento
 *
 * @author Diego Rosa dos Santos <diegosantos@alphaeditora.com.br>
 */
class PageEventManager {
    
    private static $instance;
    
    private function __construct() {
    }
    
    public function __destruct() {
    }
    
    public static function getInstance() {
        if(null === self::$instance) {
            self::$instance = new PageEventManager();
        }
        return self::$instance;
    }
    
    public function addSubscriber(SubscriberInterface $sub) {
        $events = $sub->getSubscribedEvents();
        $page = strtolower(get_class($sub));
        
        foreach($events as $type => $listenerMethod) {
            $method = explode("=>", $listenerMethod)[0];
            $redirectAction = explode("=>", $listenerMethod)[1];
            $this->listen($page, $type, $method, $redirectAction);
        }
    }
    
    /**
     * 
     * @param ProcessRequest $pageInstance
     * @param string $event
     * @param string $methodCallback
     * @param string $redirectAction
     */
    public function attach($pageInstance, $event, $methodCallback, $redirectAction) {
        $this->listen(strtolower(get_class($pageInstance)), $event, $methodCallback, $redirectAction);
    }
    
    public function listen($pageName, $eventType, $method, $redirectAction) {
        
        $data = array(
            "page" => strtolower($pageName), 
            "method" => $method, 
            "redirect" => trim($redirectAction)
        );
        $_SESSION["page_listener"][ $eventType ] = $data;
    }
    
    public function dispatch($eventType, ParameterEvent $event) {
       
        
        if($eventType == ParameterEvent::PARAMETER_ERROR) {
            $_SESSION["event_stack"] = ParameterEvent::PARAMETER_ERROR;

            $_SESSION["parametersMeta"] = $event->getParameters();
            
            $meta = $_SESSION["page_listener"][ ParameterEvent::PARAMETER_ERROR ];
            
            # a instância de ParameterEvent é destruida ao fazer o redirect
            $_SESSION["parameter_event"] = serialize($event);
            redirect($meta["page"] .'/'. $meta['redirect']);
        }
        else if($eventType == ParameterEvent::PARAMETER_SUCCESS) { 
            
        }
    }
    
    // ou mais de um
    public function executeListenerMethod($page) {
        if(isset($_SESSION["event_stack"])) {
            if($_SESSION["event_stack"] == ParameterEvent::PARAMETER_ERROR ) {
                $meta = $_SESSION["page_listener"][ ParameterEvent::PARAMETER_ERROR ];
                include_once PATH_MYFRAME . "/event/ParameterEvent.php";
                $parameterEvent = unserialize($_SESSION["parameter_event"]);
                $method = trim($meta["method"]);
                
                call_user_func(array($page, $method), $parameterEvent);
                
                $_SESSION["parameter_event"] = null;
                $_SESSION["event_stack"] = "";
                unset($_SESSION["parameter_event"]);
                unset($_SESSION["event_stack"]);
            }
        }
    }
}