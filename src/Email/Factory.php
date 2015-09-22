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
     * Retorna um objeto Email configurado com fromName e fromEmail a partir do arquivo default de configuração
     * 
     * note: Cria-se o email padrão caso nenhum tipo específico seja escolhido
     * 
     * @param  Emails|string $type  O tipo de email a ser criado use Emails::CONSTANTES
     * @return Email                Retorna a instância de um tipo de email
     */
    public static function getEmail($type = "") {
        $config = self::getConfig();
        
        if(empty($type)) {
            //cria-se o email padrão caso nenhum tipo específico seja escolhido
            $email = new Email();
        }
        else {
            $email = new $type();
        }
        
        $config->bindEmail($email);
       
        return $email;
    }
}
