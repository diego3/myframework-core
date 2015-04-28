<?php

namespace MyFrameWork;

/**
 * Classe responsável pelos trabalhos envolvendo email
 */
class MailManager {
    private $smtpHost;
    private $fromEmail;
    private $fromName;
    private $mailTitle;
    private $smtpUsername;
    private $smtpPassword;
    private $smtpPort;
    private $smtpSecure;
    private $headerMail;
    private $footerMail;
    /**
     *
     * @var PHPMailer
     */
    private $phpMailerInstance;
    /**
     *
     * @var string 
     */
    private $mailerPath;
    /**
     *
     * @var MailManager 
     */
    private static $instance;
    
    private function __construct() {
        $this->mailerPath = PATH_LOCAL . '/vendor/phpMailer/class.phpmailer.php';
        require_once ($this->mailerPath);
        $this->phpMailerInstance = new PHPMailer();
    }
    
    public static function getInstance() {
        if(is_null(self::$instance)) {
            self::$instance = new MailManager;
        }
        return self::$instance;
    }
    
    public function sendMessage($data) {
        $mcfg = parse_ini_file(PATH_APP . "/conf/mail.ini");
        
        if (empty($mcfg["smtpHost"]) || empty($mcfg["fromEmail"]) || empty($mcfg["fromName"]) || empty($mcfg["tituloEmail"])) {
            $this->erro = "Configuração de email inválido";
            return false;
        }
        require_once (PATH_LOCAL . '/vendor/phpMailer/class.phpmailer.php');
        $mail = new PHPMailer();
        $this->phpMailerInstance = $mail;
        $mail->SetLanguage("br", "phpMailer/language/");

        //$mail->SMTPDebug = true;
        $mail->CharSet = "UTF-8";
        $mail->IsSMTP();
        $mail->Host = $mcfg["smtpHost"];
        if (!empty($mcfg["smtpUsername"]) && !empty($mcfg["smtpPassword"])) {
            $mail->SMTPAuth = true;
            $mail->Username = $mcfg["smtpUsername"];
            $mail->Password = $mcfg["smtpPassword"];
        }
        if (!empty($mcfg["smtpPort"])) {
            $mail->Port = $mcfg["smtpPort"];
        }
        if (!empty($mcfg["smtpSecure"])) {
            $mail->SMTPSecure = $mcfg["smtpSecure"];
        }

        $mail->From = $mcfg["fromEmail"];
        $mail->FromName = $mcfg["fromName"];
        $mail->AddAddress($mcfg["fromEmail"], $mcfg["fromName"]);
        $mail->IsHTML(true); // set email format to HTML

        $mail->Subject = $mcfg["tituloEmail"];

        $mail->WordWrap = 50;
        $mail->Body = "\n<br>\n<br>{$data}\n<br>\n<br>\n<br>";

        $mail->AltBody = strip_tags($mail->Body);

        if (!empty($mail->ErrorInfo)) {
            echo $mail->ErrorInfo;
        }
        return $mail->Send();
    }
    
    protected function config() {
        $mcfg = parse_ini_file(PATH_APP . "/conf/mail.ini");
        $this->smtpHost = $mcfg["smtpHost"];
        $this->smtpUsername = $mcfg["smtpUsername"];
        $this->smtpPassword = $mcfg["smtpPassword"];
        $this->smtpPort     = $mcfg["smtpPort"];
        $this->fromEmail    = $mcfg["fromEmail"];
        $this->smtpSecure   = $mcfg["smtpSecure"];
        $this->fromName     = $mcfg["fromName"];
    }
    
    public function sendMessage2($title, $body) {
        $this->config();
        if (empty($this->smtpHost) || empty($this->fromEmail) || empty($this->fromName)) {
            $this->erro = "Configuração de email inválida";
            return false;
        }
        $mail = $this->phpMailerInstance;
        $mail->SetLanguage("br", "phpMailer/language/");

        //$mail->SMTPDebug = true;
        $mail->CharSet = "UTF-8";
        $mail->IsSMTP();
        $mail->Host = $this->smtpHost;
        if (!empty($this->smtpUsername) && !empty($this->smtpPassword)) {
            $mail->SMTPAuth = true;
            $mail->Username = $this->smtpUsername;
            $mail->Password = $this->smtpPassword;
        }
        if (!empty($this->smtpPort)) {
            $mail->Port = $this->smtpPort;
        }
        if (!empty($this->smtpSecure)) {
            $mail->SMTPSecure = $this->smtpSecure;
        }

        $mail->From = $this->fromEmail;
        $mail->FromName = $this->fromName;
        $mail->IsHTML(true); // set email format to HTML
        $mail->Subject = $title;
        $mail->WordWrap = 50;
        //$mail->Body = $body;
        $mail->MsgHTML($body);
        $mail->AltBody = strip_tags($mail->Body);

        if (!empty($mail->ErrorInfo)) {
            echo $mail->ErrorInfo;
        }
        return $mail->Send();
    }
    
    public function getPHPMailer() {
        return $this->phpMailerInstance;
    }
    
    public function getSmtpHost() {
        return $this->smtpHost;
    }

    public function getFromEmail() {
        return $this->fromEmail;
    }

    public function getFromName() {
        return $this->fromName;
    }

    public function getMailTitle() {
        return $this->mailTitle;
    }

    public function getSmtpUsername() {
        return $this->smtpUsername;
    }

    public function getSmtpPassword() {
        return $this->smtpPassword;
    }

    public function getSmtpPort() {
        return $this->smtpPort;
    }

    public function getSmtpSecure() {
        return $this->smtpSecure;
    }

    public function getHeaderMail() {
        return $this->headerMail;
    }

    public function getFooterMail() {
        return $this->footerMail;
    }

    public function setSmtpUsername($smtpUsername) {
        $this->smtpUsername = $smtpUsername;
        return $this;
    }

    public function setSmtpPassword($smtpPassword) {
        $this->smtpPassword = $smtpPassword;
        return $this;
    }

    public function setSmtpPort($smtpPort) {
        $this->smtpPort = $smtpPort;
        return $this;
    }
    
    public function setSmtpHost($smtpHost) {
        $this->smtpHost = $smtpHost;
        return $this;
    }

    public function setFromEmail($fromEmail) {
        $this->fromEmail = $fromEmail;
        return $this;
    }

    public function setFromName($fromName) {
        $this->fromName = $fromName;
        return $this;
    }

    public function setMailTitle($mailTitle) {
        $this->mailTitle = $mailTitle;
        return $this;
    }

    public function setSmtpSecure($smtpSecure) {
        $this->smtpSecure = $smtpSecure;
        return $this;
    }

    public function setHeaderMail($headerMail) {
        $this->headerMail = $headerMail;
        return $this;
    }

    public function setFooterMail($footerMail) {
        $this->footerMail = $footerMail;
        return $this;
    }
}
