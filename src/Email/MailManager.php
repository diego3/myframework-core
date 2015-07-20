<?php

namespace MyFrameWork\Email;

use MyFrameWork\Factory;
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
    * @var \Application\Model\Email\Smtp 
    */
    protected $smtp;
    /**
     *
     * @var \Application\Model\Email\Mailer 
     */
    protected $mailer;
    
    public function __construct(Mailer $mailer) {
        $this->mailer = $mailer;
        $this->smtp = $mailer->getSmtpServer();
    }
    
    public function smtpChecks() {
        //Create a new SMTP instance
        $smtp = new SMTP;

        //Enable connection-level debug output
        $smtp->do_debug = SMTP::DEBUG_CONNECTION;

        try {
            //Connect to an SMTP server
            if ($smtp->connect('mail.example.com', 25)) {
                //Say hello
                if ($smtp->hello($this->smtp->getSmtpHost())) { //Put your host name in here
                    //Authenticate
                    if ($smtp->authenticate('username', 'password')) {
                        return true;
                    } else {
                        throw new Exception('Authentication failed: ' . $smtp->getLastReply());
                    }
                } else {
                    throw new Exception('HELO failed: '. $smtp->getLastReply());
                }
            } else {
                throw new Exception('Connect failed');
            }
        } catch (Exception $e) {
            throw new Exception('SMTP error: '. $e->getMessage());
        }
        //Whatever happened, close the connection.
        $smtp->quit(true);
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
                Factory::log()->fatal("Mailer Send Error: " . $this->mailer->getErrorInfo());
                return false;
            } 
            return true;
        } catch (phpmailerException $e) {
            Factory::log()->fatal("phpmailerException : ". $e->errorMessage()); //Pretty error messages from PHPMailer
        } catch (\Exception $e) {
            Factory::log()->fatal("phpMailer Exception" . $e->getMessage()); //Boring error messages from anything else!
        }
        return false;
    }
    
}
