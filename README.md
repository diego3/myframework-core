[![Build Status](https://travis-ci.org/diego3/myframework-core.png?branch=master)](https://travis-ci.org/diego3/myframework-core)

O que é
--------
Micro Framework para o desenvolvimento de aplicações web.

Intenção
--------
Já pensou em escrever um CRUD em apenas 5 minutos ou menos ? pois é, com o 
MyFrameWork agora isso é possível, com poucas linhas de código você consegue criar
um CRUD completo.
Não é apenas a criação que é facilitada mas também qualquer alteração se torna rápida 
é fácil devido à flexibilidade dos componentes do MyFrameWork.

Instalação
--------

 * passo a passo do composer.json
 * baixar o skeleton  http://github/diego3/mycroframework-skeleton
 * rodar o composer  composer install
 * acessar a url que instala o banco host/painel/instalar
 * executar a aplicação

Configuração do Apache
-------

Arquivo necessário para funcionamento da aplicação

abra o arquivo httpd-vhost.conf no seu sistema operacional e adicione o seguinte:

```
<VirtualHost *:80>
     ServerName mydomain.local
     DocumentRoot "path/to/my/project/public"
     SetEnv APPLICATION_ENV "development"
     <Directory "path/to/my/project/public">
         DirectoryIndex index.php
         AllowOverride All
         Order allow,deny
         Allow from all
     </Directory>
 </VirtualHost>
