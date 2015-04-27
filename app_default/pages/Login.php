<?php

class Login extends ProcessRequest {
    
    public function __construct() {
        $this->filename = 'login';
        $this->pageTitle = 'Efetuar login';
    }
    
    public function _main() {
        return true;
    }
    
    public function _entrar() {
        $this->addParameter('usuario', 'string', array(Flag::REQUIRED));
        $this->addParameter('senha', 'string', array(Flag::REQUIRED));
        $this->addParameter('redirect', 'string', array(Flag::DEFAULT_VALUE => '/'));
        
        $this->pagedata["method"] = $this->getMethod();
        if ($this->isValidParameters()) {
            $user = $this->getParameter('usuario');
            $pass = hashit($this->getParameter('senha'));
            if (Session::singleton()->login($user, $pass)) {
                redirect($this->getParameter('redirect'));
            }
        }else {
            echo "invalid parameters";
        }
        
        $this->pagedata['erro'] = $_SESSION["logginError"];
        $this->pagedata['vemail'] = $this->getParameter('usuario');
        $this->pagedata['vredirect'] = $this->getParameter('redirect');
        return $this->_main();
    }
    
    public function _sair() {
        Session::singleton()->logout();
        redirect('/');
    }
    
    public function _recover() {
        $this->setTemplateFile("login_recover");
        return true;
    }
    
    public function _sendPassword() {
        require_once PATH_LOCAL . '/util/MailManager.php';
        $this->addParameter("email", 'string', array(Flag::REQUIRED));
        $this->cleanParameters();
        if( $this->isParametersValid() ) {
            $email = $this->getParameter("email");
            $mailer = MailManager::getInstance();
            $mailer->getPHPMailer()->AddAddress($email, ucfirst(explode("@", $email)[0]));
            
            $clientDao = Factory::DAO("cliente");
            /* @var $clientDao ClienteDAO */
            $client = $clientDao->getByEmail($email);
            if(empty($client)) {
                Session::singleton()->setData("notFoundMailDatabase", true);
                return $this->_entrar();
            }
            $password = $client["password"];
            
            $str = "<html>";
            $str .= "<body>";
            $str .= "<img src='http://alphae.com.br/app/static/image/empresa/logo_alphae.png' alt='logo_alphae'><br><br>";
            $str .= "Olá,<br>você solicitou a recuperação da sua senha para acessar sua conta em nosso site.<br><br>";
            $str .= "Seu e-mail de usuário : " .$email . "<br>";
            $str .= "Sua senha : " . $password . "<br><br>";
            $str .= "Agora você já pode acessar sua conta em <a href='http://alphae.com.br/login/entrar'>www.alphae.com.br/entrar</a><br>";
            $str .= "Att, <br>equipe Alphae!<br><br><br>A mais de 20 anos realizando sonhos!";
            $str .= "</body>";
            $str .= "</html>";
            $mailer->sendMessage2("Dados de acesso ao site www.alphae.com.br", $str);
            $erro = $mailer->getPHPMailer()->ErrorInfo;
            if(!empty($erro)) {
                echo " <h3>erro ao enviar email : " . $erro . "</h3>";
            }
            //TODO It should to show to user a success message when the mail has sent
            redirect("/");
        }
    }
    
    /**
     * Método/action utilizado(a) para redefinir a senha do usuário/cliente, 
     * lembrando que para redefinir uma nova senha o usuario deve estar logado
     * @return boolean
     */
    public function _updatepassword() {
        debug();
        //TODO enviar para o email fornecido no formulario...ok
        //TODO alterar senha no banco, criptografa-la...ok
        //TODO alterar senha no webservice...ok
        //TODO criar view de resposta para o usuario..
        require_once PATH_MYFRAME . "/MailManager.php";
        
        $this->addParameter('email', 'string', array(Flag::REQUIRED));
        $this->addParameter('newpassword', 'string', array(Flag::REQUIRED));
        $this->addParameter('confirmnewpassword', 'string', array(Flag::REQUIRED));

        $clienteDao = Factory::DAO("cliente");
        /* @var $clienteDao ClienteDAO */
        
        $this->cleanParameters();
        if( $this->isParametersValid() ) {
            $email    = $this->getParameter("email");
            $password = $this->getParameter("newpassword");
            
            if($clienteDao->userAndClientUpdatePassword($password, $email, Session::singleton()->getData("usuariointerno_id"))) {
                $mailer = MailManager::getInstance();
                $mailer->getPHPMailer()->AddAddress($email, ucfirst(explode("@", $email)[0]));

                $str = "<html>";
                $str .= "<body>";
                $str .= "<img src='http://alphae.com.br/app/static/image/empresa/logo_alphae.png' alt='logo_alphae'><br><br>";
                $str .= "Olá,<br>você solicitou sua senha e usuários para acessar nosso site, abaixo estão eles conforme solicitado.<br><br>";
                $str .= "Seu e-mail de usuário : " .$email . "<br>";
                $str .= "Sua senha : " . $password . "<br><br>";
                $str .= "Agora você já pode acessar sua conta entrando em <a href='http://alphae.com.br/login/entrar'>www.alphae.com.br/entrar</a><br>";
                $str .= "Att, <br>equipe Alphae!<br><br><br>A mais de 20 anos realizando sonhos!";
                $str .= "</body>";
                $str .= "</html>";
                $mailer->sendMessage2("Dados de acesso ao site www.alphae.com.br", $str);
                $erro = $mailer->getPHPMailer()->ErrorInfo;
                if(!empty($erro)) {
                    echo " <h3>erro ao enviar email : " . $erro . "</h3>";
                }
            }else {
                echo " nao atualizou o banco nem webservice "; exit;
            }
        }
        $this->setTemplateFile("login_updatepassword");
        return true;
    }
}

