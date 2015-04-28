O que é
--------
Framework caseiro para facilitar o densenvolvimento de aplicações web.

Intenção
--------
Ser um framework simples e produtivo no desenvolvimento web.

Instalação
--------

 * baixar o skeleton  
 * rodar o composer  [ composer install ]
 * acessar a url que instala o banco
 * executar a aplicação

Configuração do Apache
-------

Arquivo necessário para funcionamento da aplicação

abra o arquivo httpd-vhost.conf e adicione o seguinte:

```
<VirtualHost *:80>
     ServerName algumacoisa.local
     DocumentRoot "D:/site/www/project/public"
     SetEnv APPLICATION_ENV "development"
     <Directory "D:/site/www/project/public">
         DirectoryIndex index.php
         AllowOverride All
         Order allow,deny
         Allow from all
     </Directory>
 </VirtualHost>
