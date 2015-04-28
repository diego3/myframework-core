<?php

/**
 *
 * @author Diego Rosa dos Santos <diegosantos@alphaeditora.com.br>
 */
interface PagePublisherInterface {
    
    public function __construct(PageEventManager $em);
    
    /**
     * 
     * @return PageEventManager
     */
    public function getDispatcher();
}
