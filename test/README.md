Testes de unidade
--------
Esse diretório contém todos os testes de unidade das classes e compomentes do framework.
Para conseguir rodar os testes na sua máquina certifique-se que não esqueceu nenhum destes passos.

Composer
------
rode o composer estando na raiz do projeto

```shell
$ composer self-update
$ composer update
```

Banco de dados
-------
Os DAOs e alguns testes precisam conectar-se ao banco de dados para conseguir rodar os testes!

A conexão é realizada a partir dos parâmetros informandos no arquivo **test/conf/database.local.ini**, portanto não esqueça de configurá-lo.

Arquivo .dist e .ini
--------

o arquivo .dist serve apenas como versão de distribuição, basta remover a extensão .dist para que o mesmo fique na extensão .ini a qual 
o sistema vai enxergar como arquivo de configuração válido para realizar as conexões!
