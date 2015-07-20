<?php

namespace MyFrameWork\Email;

use MyFrameWork\Email\AbstractEmail;
use MyFrameWork\Email\Smtp;
use MyFrameWork\Factory as Logger;

/**
 * 
 *
 * @author Diego Rosa dos Santos <diegosantos@alphaeditora.com.br>
 */
class Config {

    /**
     * Armazena o caminho completo para o arquivo de configuração de email
     * 
     * @var string 
     */
    protected $filename;

    /**
     * 
     * @param string $filename O caminho completo para o arquivo de configuração de email
     * @return \Application\Model\Email\Config
     */
    public function setFilename($filename) {
        if(startsWith($filename, "/")) {
            $filename = substr($filename, 1);
        }
        
        //remover barras duplicadas
        if (strpos($filename, "//", 0) !== FALSE) {
            $filename = str_replace("//", "/", $filename);
        }
        
        if(endsWith($filename, "/")) {
            $filename = substr($filename, 0, -1);
        }
        $this->filename = $filename;
        return $this;
    }
    
    /**
     * 
     * @return string O caminho completo para o arquivo de configuração de email
     */
    public function getFilename() {
        return $this->filename;
    }

    /**
     * 
     * @param string $filename  O caminho completo para o arquivo de configuração de email
     */
    public function __construct($filename = "") {
        if (!empty($filename)) {
            $this->setFilename($filename);
        }
    }

    /**
     *  As configurações são retornadas como um array associativo
     *  
     * @return array Retorna um array vazio em casos de falha
     */
    public function load() {
        if (!file_exists($this->filename)) {
            Logger::log()->fatal("Arquivo de configuração de email não foi encontrado no path {$this->filename}");
            return array();
        }
        $has_parsed = parse_ini_file($this->filename, true);
        if (!$has_parsed) {
            return array();
        }
        return $has_parsed;
    }

    /**
     * Pré configura um servidor Smtp
     * 
     * @param Smtp $smtp  Um servidor Smtp
     * @return boolean 
     */
    public function bindSmtp(Smtp $smtp) {
        $props = $this->load();
        if(empty($props)) {
            return false;
        }
        
        $smtp->setSmtpHost($props["mail"]["smtpHost"])
                ->setSmtpPassword($props["mail"]["smtpPassword"])
                ->setSmtpPort($props["mail"]["smtpPort"])
                ->setSmtpSecure($props["mail"]["smtpSecure"])
                ->setSmtpUsername($props["mail"]["smtpUsername"]);
        return true;
    }

    /**
     * Pré configura um email para uso
     * 
     * @param  AbstractEmail  $email  Uma implementação de AbstractEmail
     * @return boolean      
     */
    public function bindEmail(AbstractEmail $email) {
        $props = $this->load();
        if(empty($props)) {
            return false;
        }
        $email->setFromEmail($props["mail"]["fromEmail"])
                ->setFromName($props["mail"]["fromName"]);
        return true;
    }

}
