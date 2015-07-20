<?php

namespace MyFrameWork\Email\Exception;

/**
 * Description of InvalidMailerException
 *
 * @author Diego Rosa dos Santos <diegosantos@alphaeditora.com.br>
 */
class InvalidMailerException extends \Exception {
    
    protected $mailer;
    
    /**
     * 
     * @param string|Object $mailer
     */
    public function mailer($mailer) {
        if(is_string($mailer)) {
            $this->mailer = $mailer;
        }
        else if(is_object($mailer)) {
            $this->mailer = get_class($mailer);
        }
        
        $this->message = sprintf("Invalid Mailer Class! %s You should implements the Mailer Interface! ", $this->mailer);
    }
    
}
