<?php

/**
 *
 * @author Diego Rosa dos Santos <diegosantos@alphaeditora.com.br>
 */
interface PublisherInterface {
    
    public function __construct(EventManager $em);
    
    /**
     * 
     * @return EventManager2
     */
    public function getDispatcher();
}
