<?php

namespace MyFrameWork\Email;

/**
 * 
 * Classe abstrata que descreve as propriedades de um email comum.
 * 
 * @author Diego Rosa dos Santos <diegosantos@alphaeditora.com.br>
 */
abstract class AbstractEmail {
    /**
     * O email de quem está enviando
     * 
     * @var string 
     */
    protected $fromEmail;
    /**
     * O nome de quem está enviando
     * 
     * @var string
     */
    protected $fromName;
    /**
     * Armazena a lista de endereços dos destinatários para esse e-mail
     * 
     * @var array
     */
    protected $to = [];
    /**
     * Armazena a lista de endereços em modo cópia para esse e-mail
     * 
     * @var array
     */
    protected $cc = [];
    /**
     * Armazena a lista de endereços em modo cópia oculta
     * 
     * @var array
     */
    protected $bcc = [];
    /**
     * A mensagem que será enviada ao(s) destinário(s)
     * 
     * @var string 
     */
    protected $message;
    /**
     * O assunto ou títuloo do email, como preferir :)
     * 
     * @var string  
     */
    protected $assunto;
    /**
     * Armazena arquivos a serem anexados no corpo do email
     * 
     * @var array Lista de anexos
     */
    protected $anexos = [];
    
    /**
     * Adiciona um arquivo na lista dos anexos.
     * O arquivo deve existir no sistema de arquivos.
     * 
     * @param string $anexo Path para um arquivo. 
     */
    public function addAnexo($anexo) {
        if(file_exists($anexo)) {
            $this->anexos[] = $anexo;
        }
    }
    
    /**
     * Retorna todos os destinatários deste email
     * 
     * @return array
     */
    public function getTo() {
        return $this->to;
    }
    
    /**
     * Retorna todos os destinatários em modo cópia
     * 
     * @return array
     */
    public function getCc() {
        return $this->cc;
    }
    
    /**
     * Retorna todos os destinatários em modo cópia OCULTA
     * 
     * @return array
     */
    public function getBCc() {
        return $this->bcc;
    }
    
    /**
     * Adiciona um destinatário 
     * 
     * @param  string $toEmail Endereço de e-mail do destinatário
     * @param  stirng $toName  O nome do destinatário
     * @return Email
     */
    public function addTo($toEmail, $toName) {
        $this->to[] = [ 
            "email" => $toEmail, 
            "name"  => $toName 
        ];
        return $this;
    }
    
    /**
     * Limpa todos os endereços TO
     */
    public function clearTo() {
        $this->to = [];
    }
    
    /**
     * Limpa todos os endereços CC
     */
    public function clearCc() {
        $this->cc = [];
    }
    
    /**
     * Limpa todos os endereços BCC
     */
    public function clearBCc() {
        $this->bcc = [];
    }
    
     /**
     * Adiciona um destinatário no modo cópia
     * 
     * @param  string $toEmail Endereço de e-mail do destinatário
     * @param  stirng $toName  O nome do destinatário
     * @return Email
     */
    public function addCc($toEmail, $toName) {
        $this->cc[] = [ 
            "email" => $toEmail, 
            "name"  => $toName 
        ];
        return $this;
    }
    
    /**
     * Adiciona um destinatário no modo cópia oculta
     * 
     * @param  string $toEmail Endereço de e-mail do destinatário
     * @param  stirng $toName  O nome do destinatário
     * @return Email
     */
    public function addBCc($toEmail, $toName) {
        $this->bcc[] = [ 
            "email" => $toEmail, 
            "name"  => $toName 
        ];
        return $this;
    }
    
    public function getAssunto() {
        return $this->assunto;
    }

    public function setAssunto($assunto) {
        $this->assunto = $assunto;
        return $this;
    }

    public function getAnexos() {
        return $this->anexos;
    }
    
    public function getFromEmail() {
        return $this->fromEmail;
    }

    public function getFromName() {
        return $this->fromName;
    }

    public function getMessage() {
        return $this->message;
    }

    public function setFromEmail($fromEmail) {
        $this->fromEmail = $fromEmail;
        return $this;
    }

    public function setFromName($fromName) {
        $this->fromName = $fromName;
        return $this;
    }

    public function setMessage($message) {
        $this->message = $message;
        return $this;
    }
}
