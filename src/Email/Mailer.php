<?php

namespace MyFrameWork\Email;

/**
 *  
 * Contrato para todos os provedores de serviços de email.
 * 
 * FIX-ME refatorar pois viola o princípio da Segregação de interfaces
 * 
 * @author Diego Rosa dos Santos <diegosantos@alphaeditora.com.br>
 */
interface Mailer {
    /*@ tipos de mecaminos de envio de email disponíveis */
    const SEND_MAIL = "sendmail";
    const QMAIL     = "qmail";
    const SMTP      = "smtp";
    const MAIL      = "mail";
    
    /**
     * Envia a mensagem de email
     */
    public function send();
    
    /**
     * O método utilizado internamente para enviar o email
     * 
     * @param const $strategy  Utilizar Mailer constantes
     */
    public function senderStrategy($strategy);
    
    /**
     * Refere-se aquele(a) que está enviando o email, o remetente
     * 
     * @param string $fromEmail
     * @param string $fromName
     */
    public function setFrom($fromEmail, $fromName);
    
    /**
     * Refere-se aquele(a) que está recebendo o email, o destinatário
     * 
     * @param array $to
     */
    public function setTo(array $to);
    
    /**
     * Configura os endereços destinatários que vão em modo cópia
     * 
     * @param array $cc
     */
    public function setCc(array $cc);
    
    /**
     * Configura os endereços destinatários que vão em modo cópia oculta
     * 
     * @param array $bccS
     */
    public function setBCc(array $bccS);
    
    /**
     * 
     * @param array $anexos
     */
    public function addAttachments(array $anexos);
    
    /**
     * 
     * @param string $content
     */
    public function setMessage($content); 
    
    /**
     * O assunto do email
     * 
     * @param string $subject
     */
    public function setSubject($subject);
    
    /**
     * 
     */
    public function getErrorInfo();
    
    /**
     * Pergunta se está em modo html ou não
     * 
     * @return boolean
     */
    public function isHtml();
    
    /**
     * Ativa o uso do html
     * 
     * @return \MyFrameWork\Email\PhpMailer
     */
    public function enableHtml();
    
    /**
     * Desativa o uso do html
     * 
     * @return \MyFrameWork\Email\PhpMailer
     */
    public function disableHtml();
}
