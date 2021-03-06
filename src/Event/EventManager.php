<?php

namespace MyFrameWork\Event;

use MyFrameWork\Event\Event;
use MyFrameWork\Event\SubscriberInterface;

/**
 * Description of EventManager
 *
 * @author Diego Rosa dos Santos <diegosantos@alphaeditora.com.br>
 */
class EventManager {
    /**
     *
     * @var EventManager 
     */
    protected static $instance;
    /**
     *
     * @var array Lista de SubscriberInterface 
     */
    protected $listeners = array();
    
    /**
     * use getInstance method instead
     */
    private function __construct() {}
    
    /**
     * 
     * @return EventManager
     */
    public static function getInstance() {
        if(null === self::$instance) {
            self::$instance = new EventManager();
        }
        return self::$instance;
    }
    
    /**
     * 
     * @param string $eventType
     * @param Callable $callback
     */
    public function listen($eventType, $callback){
        $this->listeners[$eventType][] = $callback;
    }
    
    /**
     * 
     * @param string $eventName
     * @param Event $event
     */
    public function dispatch($eventName, Event $event) {
        if($this->hasListeners($eventName)) {
            # $listener eh um callback [ Closure, function, classe ]
            foreach($this->listeners[$eventName] as $listener) {
                call_user_func_array($listener/*SubscriberClass*/, array($event)/*EventParameter*/);
            }
        }
    }
    
    /**
     * Verifica se há algum listener registrado antes de dispachar o evento
     * 
     * @param string $eventName O nome de um evento
     * @return boolean          Retorna successo se há algum listener registrado para tal evento ou falso caso contrário
     */
    public function hasListeners($eventName) {
        $has = false;
        foreach ($this->listeners as $value) {
            if(isset($this->listeners[$eventName])) {
                $has = true;
                break;
            }
        }
        return $has;
    }
    
    /**
     * Registra um ouvinte/listener interessado em determinado(s) evento(s)
     * @param SubscriberInterface $sub
     */
    public function addSubscriber(SubscriberInterface $sub) {
        $listeners = $sub->getSubscribedEvents();
        
        foreach($listeners as $eventType => $listenerMethod) {
            $this->listen($eventType, array($sub, $listenerMethod));
        }
    }
    
    /**
     * Alias para addSubscriber
     * @param SubscriberInterface $sub
     */
    public function addListener(SubscriberInterface $sub) {
        $this->addSubscriber($sub);
    }
}
