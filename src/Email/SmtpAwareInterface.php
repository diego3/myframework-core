<?php

namespace MyFrameWork\Email;

/**
 *
 * @author Diego Rosa dos Santos <diegosantos@alphaeditora.com.br>
 */
interface SmtpAwareInterface {
    
    public function setSmtpServer($smtp);
    
    public function getSmtpServer();
    
}
