<?php



/**
 * Description of Event
 *
 * @author Diego Rosa dos Santos <diegosantos@alphaeditora.com.br>
 */
class Event {
    
    private $eventName;
    
    public function setName($eventName) {
        $this->eventName = $eventName;
    }
    
    public function getName() {
        return $this->eventName;
    }
    
}
