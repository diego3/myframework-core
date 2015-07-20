<?php

namespace MyFrameWork\Email;

use MyFrameWork\Email\MailManager;
use MyFrameWork\Email\Config;
use MyFrameWork\Email\Smtp;
use MyFrameWork\Email\Email;
use MyFrameWork\Email\PhpMailer;
use MyFrameWork\Email\Mailer;
use MyFrameWork\Email\Mailers;

use MyFrameWork\Email\Exception\InvalidMailerException;

/**
 * Factory para os principais componentes do sistema de email
 *
 * @author Diego Rosa dos Santos <diegosantos@alphaeditora.com.br>
 */
class Factory {
    /**
     *
     * @var \Application\Model\Email\MailManager 
     */
    protected static $manager;
    /**
     *
     * @var \Application\Model\Email\Config
     */
    protected static $config;
    
    /**
     * 
     * @param  const  $type   Mailers::CONSTANTS
     * @return Mailer
     * @throws InvalidMailerException
     */
    public static function getMailManager($type) {
        if(null === static::$manager){
            $config = self::getConfig();
            
            $smtp = new Smtp();
            $config->bindSmtp($smtp);
            
            $mailer = new $type();
            if(!$mailer instanceof Mailer) {
                throw (new InvalidMailerException())->mailer($mailer);
            } 
            $mailer->setSmtpServer($smtp);
            static::$manager = new MailManager($mailer);
        }
        return static::$manager;
    }
    
    protected static function getConfig() {
        if(null === static::$config) {
            $config = new Config(PATH_LOCAL . "/" . EMAIL_CONFIG_FILE);
            static::$config = $config;
        }
        return static::$config;
    }
    
    /**
     * Retorna um Email configurado com fromName e fromEmail a partir do arquivo default de configuração
     * 
     * @return Email  Retorna um Email configurado
     */
    public static function getEmail() {
        $config = self::getConfig();
        
        $email = new Email();
        $config->bindEmail($email);
       
        return $email;
    }
    
   
    
}
