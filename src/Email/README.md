Como usar
---------

 * uso básico

```php

$manager = Factory::getMailManager(Mailers::PHP);
$email  = Factory::getEmail();

$email->setMessage("Mensagem que vai no corpo do email");
$email->setAssunto("Assunto do email!");
$email->addTo("destinatario@alphaeditora.com.br", "Nome do destinatário");

$manager->send($email);
```

 * Anexando arquivos

```php

$manager = Factory::getMailManager(Mailers::PHP);
$email   = Factory::getEmail();

$email->addAnexo("path/to/file/relatorios_vendas.xls");
$email->addAnexo("path/to/file/relatorios_marketing.xls");
//... e outros
$email->setAssunto("Relatório mensal do setor de vendas e marketing!");
$email->setMessage("Segue em anexo conforme solicitado!");
$email->addTo("destinatario@alphaeditora.com.br", "Nome do destinatário");

$manager->send($email);
```
 > O arquivo a ser anexado **deve** existir no sistema de arquivos do servidor, ou seja,
caso precise anexar arquivos do cliente então será necessário que o cliente faça o upload
desses arquivos para em seguida ser informado o caminho dos mesmos no servidor  

 * Enviado email com cópia para outras pessoas

```php

$manager = Factory::getMailManager(Mailers::PHP);
$email   = Factory::getEmail();

$email->addTo("destinatario@alphaeditora.com.br", "Nome do destinatário");
//cópia simples
$email->addCc("diegosantos@alphaeditora.com.br", "Diego Santos");
//cópia oculta
$email->addBCc("gerente@alphaeditora.com.br", "Gerente SobreNome");
$email->setMessage("Mensagem que vai no corpo do email");

$manager->send($email);
```

 > Não esqueça dos 'use' no início do seu arquivo.
 
 > Por padrão não é necessário configurar o remetente e destinatário, pois os mesmos
   já estão previamente configurados no email.ini. Pode-se também sobre-escreve-los programaticamente
   caso seja necessário.
