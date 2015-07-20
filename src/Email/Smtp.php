<?php

namespace MyFrameWork\Email;

/**
 * Descreve as propriedades de um servidor SMTP
 *
 * @author Diego Rosa dos Santos <diegosantos@alphaeditora.com.br>
 */
class Smtp {
    protected $smtpHost;// = smtp.gmail.com
    protected $smtpPort;// = 465 
    
    protected $smtpSecure;// = tsl
    
    protected $smtpUsername;// = info@alphaeditora.com.br
    protected $smtpPassword;// = "passwordhere"
    
    
    public function getSmtpHost() {
        return $this->smtpHost;
    }

    public function getSmtpPort() {
        return $this->smtpPort;
    }

    public function getSmtpSecure() {
        return $this->smtpSecure;
    }

    public function getSmtpUsername() {
        return $this->smtpUsername;
    }

    public function getSmtpPassword() {
        return $this->smtpPassword;
    }

    public function setSmtpHost($smtpHost) {
        $this->smtpHost = $smtpHost;
        return $this;
    }

    public function setSmtpPort($smtpPort) {
        $this->smtpPort = $smtpPort;
        return $this;
    }

    public function setSmtpSecure($smtpSecure) {
        $this->smtpSecure = $smtpSecure;
        return $this;
    }

    public function setSmtpUsername($smtpUsername) {
        $this->smtpUsername = $smtpUsername;
        return $this;
    }

    public function setSmtpPassword($smtpPassword) {
        $this->smtpPassword = $smtpPassword;
        return $this;
    }


}
