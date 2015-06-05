<?php

namespace MyFrameWork\Event;

/**
 *
 * @author Diego Rosa dos Santos <diegosantos@alphaeditora.com.br>
 */
interface PublisherInterface {
    
    /**
     * Armazena o EventManager. 
     * 
     * @param \MyFrameWork\Event\EventManager $em
     */
    public function setEventManager(EventManager $em);
    
    
    /**
     * Retorna o EventManager
     * 
     * @return \MyFrameWork\Event\EventManager 
     */
    public function getDispatcher();
}
