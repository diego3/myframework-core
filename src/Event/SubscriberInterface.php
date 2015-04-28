<?php

namespace MyFrameWork\Event;

/**
 *
 * @author Diego Rosa dos Santos <diegosantos@alphaeditora.com.br>
 */
interface SubscriberInterface {
   
    /**
     * 
     *   Retorna os eventos de interesse
     *   return array(
     *      'event_name' => 'listener_function',
     *      'other_event_name' => 'other_listener_method'
     *      //and so on...
     *   )
     * 
     * @return array
     */
    public function getSubscribedEvents();
}
