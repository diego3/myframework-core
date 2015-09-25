<?php

namespace MyFrameWork\Email;

use MyFrameWork\Factory as Logger;
use MyFrameWork\Template;

use MyFrameWork\Email\AbstractEmail;
use MyFrameWork\Email\Mailer;

/**
 * Descreve um gerenciador de emails
 *
 * @author Diego Rosa dos Santos <diegosantos@alphaeditora.com.br>
 */
class MailManager {
   /**
    *
    * @var \MyFrameWork\Email\Smtp 
    */
    protected $smtp;
    /**
     *
     * @var \MyFrameWork\Email\Mailer 
     */
    protected $mailer;
    
    public function __construct(Mailer $mailer) {
        $this->mailer = $mailer;
        $this->smtp = $mailer->getSmtpServer();
    }
    
    public function smtpChecks() {
        return $this->mailer->checkSmtp();
    }
    
    /**
     * 
     * @return \MyFrameWork\Email\Mailer 
     */
    public function getMailer() {
        return $this->mailer;
    }
    
    public function send(AbstractEmail $email) {
        try{
            //Set who the message is to be sent from
            $this->mailer->setFrom($email->getFromEmail(), $email->getFromName());
            
            //Set an alternative reply-to address
            //$mail->addReplyTo($email->getFromEmail(), $email->getFromName());

            $this->mailer->setTo($email->getTo());
            $this->mailer->setCc($email->getCc());
            $this->mailer->setBCc($email->getBCc());
            $this->mailer->setSubject($email->getAssunto());
            
            
            if($email instanceof \MyFrameWork\Email\HtmlInterface) {
                
                $mustache = Template::singleton();
                $html_body = $mustache->renderHTML(file_get_contents($email->getTemplatePath()), $email->getTemplateParams());
                $this->mailer->setMessage($html_body  /*, dirname(__FILE__)*/);
            }
            else {
                $this->mailer->disableHtml();
                $this->mailer->setMessage($email->getMessage());
            }
            
            //Replace the plain text body with one created manually
            //$mail->AltBody = 'This is a plain-text message body';
            
            $this->mailer->addAttachments($email->getAnexos());
            
            //send the message, check for errors
            if (!$this->mailer->send()) {
                Logger::log()->fatal("Mailer Send Error: " . $this->mailer->getErrorInfo());
                return false;
            } 
            return true;
        } catch (phpmailerException $e) {
            Logger::log()->fatal("phpmailer Exception : ". $e->errorMessage()); //Pretty error messages from PHPMailer
        } catch (\Exception $e) {
            Logger::log()->fatal("phpMailer Exception : " . $e->getMessage()); //Boring error messages from anything else!
        }
        return false;
    }
}
