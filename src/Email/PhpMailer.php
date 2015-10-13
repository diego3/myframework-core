<?php

namespace MyFrameWork\Email;

use MyFrameWork\Email\Mailer;
use MyFrameWork\Email\SmtpAwareInterface;

/**
 * Classe que encapsula o real PHPMailer 
 *
 * @author Diego Rosa dos Santos <diegosantos@alphaeditora.com.br>
 */
class PhpMailer implements Mailer , SmtpAwareInterface {
    /**
     *
     * @var  \Application\Model\Email\Smtp
     */
    protected $smtp;
    /**
     * Armazena o real PHPMailer
     * @var \PHPMailer 
     */
    protected $php_mailer;
    /**
     * O método utilizado internamente para enviar o email
     * 
     * @var string Utilizar Mailer constantes
     */
    protected $method;
    /**
     *
     * @var boolean 
     */
    protected $ishtml = false;
    /**
     * Armazena o estado de debug do smtp.
     * 
     * Use debugSmtp(true) para ativar ou passe false para desativar novamente
     * 
     * @var boolean 
     */
    protected $debug_smtp = false;
    
    /**
     * Ativa ou desativa o modo debug para o smtp.
     * 
     * Se ativo será exibido várias informações referente às requisições entre os protocolos
     * 
     * @param boolean $boolean Use 'true' para ativar ou 'false' para desativar novamente
     */
    public function debugSmtp($boolean) {
        $this->debug_smtp = $boolean;
    }
    
    public function __construct() {
        $this->php_mailer = new \PHPMailer;
        $this->php_mailer->CharSet = "UTF-8";
    }
    
    public function setSmtpServer($smtp) {
        $this->smtp = $smtp;
    }
    
    public function getSmtpServer() {
        return $this->smtp;
    }
    
    /**
     * Envia o email usando smtp como servico default
     * 
     * @return boolean
     * @throws Exception
     */
    public function send() {
        $this->chose_sender_strategy();
        $this->configure_smtp_parameters();
        
        try{
            return $this->php_mailer->send();
        }catch (phpmailerException $e){
            throw new Exception($e->getMessage());
        }    
    }
    
    public function checkSmtp() {
        //Create a new SMTP instance
        $smtp = new \SMTP;

        //Enable connection-level debug output
        $smtp->do_debug = \SMTP::DEBUG_CONNECTION;

        try {
            //Connect to an SMTP server
            if ($smtp->connect($this->getSmtpServer()->getSmtpHost(), $this->getSmtpServer()->getSmtpPort())) {
                //Say hello
                if ($smtp->hello($this->smtp->getSmtpHost())) { //Put your host name in here
                    //Authenticate
                    dump($this->getSmtpServer()->getSmtpUsername());
                    dump($this->getSmtpServer()->getSmtpPassword());
                    if ($smtp->authenticate($this->getSmtpServer()->getSmtpUsername(), $this->getSmtpServer()->getSmtpPassword())) {
                        return true;
                    } else {
                        throw new \Exception('Authentication failed: ' . $smtp->getLastReply());
                    }
                } else {
                    throw new \Exception('HELO failed: '. $smtp->getLastReply());
                }
            } else {
                throw new \Exception('Connect failed');
            }
        } catch (\Exception $e) {
            throw new \Exception('SMTP error: '. $e->getMessage());
        }
        //Whatever happened, close the connection.
        $smtp->quit(true);
    }
    
    /**
     * O método utilizado internamente para enviar o email.
     * 
     * @param const $strategy  Utilizar Mailer constantes
     */
    public function senderStrategy($strategy) {
        $this->method = $strategy;
    }

    public function setFrom($fromEmail, $fromName) {
        $this->php_mailer->setFrom($fromEmail, $fromName);
    }

    public function setTo(array $tos) {
        foreach($tos as $to){
            $this->php_mailer->addAddress($to["email"], $to["name"]);
        }
    }
    
    public function setCc(array $ccS) {
        foreach($ccS as $cc) {
            $this->php_mailer->addCC($cc["email"], $cc["name"]);
        }
    }
    
    public function setBCc(array $bccS) {
        foreach($bccS as $bcc){
            $this->php_mailer->addBCC($bcc["email"], $bcc["name"]);
        }
    }
    
    protected function chose_sender_strategy() {
        switch($this->method) {
            case Mailer::MAIL:
                $this->php_mailer->isMail();
                break;
            case Mailer::QMAIL:
                $this->php_mailer->isQmail();
                break;
            case Mailer::SEND_MAIL:
                $this->php_mailer->isSendmail();
                break;
            default :
                $this->php_mailer->isSMTP();
                $this->method = Mailer::SMTP;
        }
    }
    
    protected function configure_smtp_parameters() {
        if($this->method == Mailer::SMTP) {
            // 0 = off (for production use)
            $this->php_mailer->SMTPDebug = 0;
            
            $this->php_mailer->CharSet = "UTF-8";
            
            if($this->debug_smtp) {
                //Enable SMTP debugging
                // 0 = off (for production use)
                // 1 = client messages
                // 2 = client and server messages
                $this->php_mailer->SMTPDebug = 2;

                //Ask for HTML-friendly debug output
                $this->php_mailer->Debugoutput = 'html';
            }
            //Set the hostname of the mail server
            $this->php_mailer->Host = $this->smtp->getSmtpHost();

            //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
            $this->php_mailer->Port = $this->smtp->getSmtpPort();

            //Set the encryption system to use - ssl (deprecated) or tls
            $this->php_mailer->SMTPSecure = $this->smtp->getSmtpSecure();

            //Whether to use SMTP authentication
            $this->php_mailer->SMTPAuth = true;

            //Username to use for SMTP authentication - use full email address for gmail
            $this->php_mailer->Username = $this->smtp->getSmtpUsername();

            //Password to use for SMTP authentication
            $this->php_mailer->Password = $this->smtp->getSmtpPassword();
        }
    }
    
    /**
     * Exemplo minimo de conexão com o smtp funcionando
     * 
     * @param string $user        Seu email 
     * @param string $password    A senha do seu email
     * @param string $to          O email de destino
     */
    public function smtpConnectivityTest($user, $password, $to) {
        $mail = new \PHPMailer();

        // ---------- adjust these lines ---------------------------------------
        $mail->Username = $user; // your GMail user name
        $mail->Password = $password; 
        $mail->AddAddress($to); // recipients email
        $mail->FromName = "your name"; // readable name

        $mail->Subject = "Subject title";
        $mail->Body    = "Here is the message you want to send to your friend."; 
        //-----------------------------------------------------------------------
        
        $mail->SMTPDebug = 2;
        $mail->Debugoutput = 'html';
        
        $mail->Host = "ssl://smtp.gmail.com"; // GMail
        $mail->Port = 465;
        $mail->IsSMTP(); // use SMTP
        $mail->SMTPAuth = true; // turn on SMTP authentication
        $mail->From = $mail->Username;
        if(!$mail->Send())
            echo "Mailer Error: " . $mail->ErrorInfo;
        else
            echo "Message has been sent";
    }
    
    /**
     * 
     * @param array $anexos 
     * @see Email->addAnexo
     */
    public function addAttachments(array $anexos) {
        if(!empty($anexos)) {
            foreach($anexos as $anexo_path) {
                $this->php_mailer->addAttachment($anexo_path);
            }
        }
    }

    public function setMessage($content) {
        if($this->isHtml()) {
            $this->php_mailer->msgHTML($content);
        }
        else {
            $this->php_mailer->Body = $content;
        }
    }

    public function setSubject($subject) {
        $this->php_mailer->Subject = $subject;
    }
    
    public function getErrorInfo() {
        return $this->php_mailer->ErrorInfo; 
    }
    
    /**
     * Pergunta se está em modo html ou não
     * 
     * @return boolean
     */
    public function isHtml() {
        return $this->ishtml;
    }
    
    /**
     * Ativa o uso do html
     * 
     * @return \MyFrameWork\Email\PhpMailer
     */
    public function enableHtml() {
        $this->ishtml = true;
        return $this;
    }
    
    /**
     * Desativa o uso do html
     * 
     * @return \MyFrameWork\Email\PhpMailer
     */
    public function disableHtml() {
        $this->ishtml = false;
        return $this;
    }
}
