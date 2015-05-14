<?php

namespace MyFrameWork\Enum;

use MyFrameWork\Enum\BasicEnum;

abstract class Flag extends BasicEnum {
    /**
     * Define o valor padrão utilizado para um atributo
     * @var mixed
     */
    const DEFAULT_VALUE = 'defaultvalue';
    
    /**
     * Define se o campo é obrigatório ou não
     * @var boolean
     */
    const REQUIRED = 'required';
    
    /**
     * Define uma máscara para representar o valor no formato humano
     * Atualmente suporta apenas o formato de mascara utilizado no printf para um único valor
     * @var string
     */
    const MASK = 'mask';
    
    //Text Parameters
    /**
     * Define o número máximo de caracteres permitido
     * @var int
     */
    const MAXLENGHT = 'maxlenght';
    
    /**
     * Define o número minimo de caracteres para o campo
     * Verificado apenas se o parâmetro é obrigatório ou o campo não é vazio 
     * @var int
     */
    const MINLENGHT = 'minlenght';
    
    /**
     * Define se o tamanho máximo for ultrapassado ele deve ser truncado ou não
     * @var boolean
     */
    const TRUNCATE = 'truncate';
    
    /**
     * Define que serão removidos os caracteres vazios do início e final do texto
     * @var boolean
     */
    const TRIM = 'trim';
    
    /**
     * Define que ao invés de removida as tags serão codificadas em caracteres entities
     * @var boolean
     */
    const ENCODE_TAGS = 'encodetags';

    /**
     * Define que o conteúdo deve possuir caracteres de a-Z, 0-9 e espaços
     * @var boolean
     */
    const ALNUM = 'alnum';
    
    /**
     * Define que o conteúdo deve possuir apenas caracteres de a-Z e espaços
     * @var boolean
     */
    const ALPHA = 'alpha';
    
    /**
     * Define que o conteúdo deve possuir apenas caracteres consoantes e espaços
     * @var boolean
     */
    const CONSONANT = 'consonant';
    
    /**
     * Define que o conteúdo deve possuir apenas caracteres de 0-9 e espaços
     * @var boolean
     */
    const DIGIT = 'digit';
    
    /**
     * Define que o conteúdo deve possuir apenas caracteres visíveis
     * Não aceita caracteres de controle e espaço
     * @var boolean
     */
    const PRNT = 'prnt';
    
    /**
     * Define que o texto deverá ser minúsculo
     * @var boolean
     */
    const LOWERCASE = 'lowercase';
    
    /**
     * Define que o texto não poderá ter espaços
     * @var boolean
     */
    const NOWHITESPACE = 'nowhitespace';
    
    /**
     * Define que o texto deverá ter somente caracteres de pontuação
     * @var boolean
     */
    const PUNCT = 'punct';
    
    /**
     * Define que o texto deverá ser maiúsculo
     * @var boolean
     */
    const UPPERCASE = 'uppercase';
    
    /**
     * Define que o texto aceita apenas vogais
     * @var boolean
     */
    const VOWEL = 'vowel';
    
    /**
     * Define que o texto aceita apenas valores hexadecimais
     * @var boolean
     */
    const XDIGIT = 'xdigit';
    
    //Number parameters
    /**
     * Define o menor valor numérico aceito
     * @var numeric
     */
    const MIN_VALUE_INCLUSIVE = 'minvalueinclusive';
    
    /**
     * Define o maior valor numérico aceito
     * @var numeric
     */
    const MAX_VALUE_INCLUSIVE = 'maxvalueinclusive';
    
    /**
     * Define o numero de casas decimais
     * @var int
     */
    const DECIMAL_SIZE = 'decimalsize';
    
    /**
     * Define que o número deverá ser maior que zero
     * @var boolean
     */
    const POSITIVE_NUMBER = 'positive';
    
    /**
     * Define que o número deverá ser menor do que zero
     * @var boolean
     */
    const NEGATIVE_NUMBER = 'negative';
    
    //Boolean
    /**
     * Define qual o valor textual deverá ser utilizado para representar o valor lógico true
     * @var string O valor padrão é 'verdadeiro'
     */
    const TRUE_LABEL = 'true';
    
    /**
     * Define qual o valor textual deverá ser utilizado para representar o valor lógico false
     * @var string O valor padrão é 'falso'
     */
    const FALSE_LABEL = 'false';
    
    //Email e URL
    /**
     * Define se o domínio deve ou não ser válido para o email/url
     * @var boolean O padrão é false
     */
    const VALIDATE_DOMAIN = 'validatedomain';
    
    //Primary key
    /**
     * Define o nome da classe DAO que será carregada para validar a chave primária
     * @var string
     */
    const DAO_NAME = 'daoname';
    
    /**
     * Define qual o campo do banco de dados será exibido para o usuário 
     * $var string
     */
    const DAO_LABEL = 'name';
    
    /**
     * Define qual campo do banco de dados será usado para preencher o atributo 'value' do elemento option
     * @var string 
     */
    const DAO_VALUE = 'id';
    
    /**
     * Define se irá ser considerado apenas campos ativos, o padrão é true
     * @var boolean O padrão é true
     */
    const ONLY_ACTIVE = 'onlyeactive';
    
    //Enum
    /**
     * Define o nome da classe enum que será carregada para validar o valor
     * @var string
     */
    const ENUM_NAME = 'enum';
    
    //Data, time and datetime
    /**
     * Define o formato da data
     * @var string
     */
    const DATE_FORMAT = 'dateformat';
    
    /**
     * Formato da data no padrão ISO ano-mes-dia
     * @var boolean
     */
    const DATE_FORMAT_ISO = 'Y-m-d';
    
    /**
     * Formato da data no padrão americano mes-dia-ano
     * @var boolean
     */
    const DATE_FORMAT_USA = 'm-d-Y';
    
    /**
     * Formato da data no padrão brasileiro dia-mes-ano
     * Este será o valor padrão da data
     * @var boolean
     */
    const DATE_FORMAT_BRAZIL = 'd-m-Y';
    
    //File and Medias
    /**
     * Define qual o local em que o arquivo deverá ser salvo
     * @var string
     */
    const MOVE_TO = 'moveto';
    
    //Render params
    /**
     * Define qual o nome do renderizados para o campo em questão
     * Se nenhum for informado será utilizado o padrão para o tipo em questão
     * @var string
     */
    const RENDER = 'render';
    
    /**
     * O esquema de template que o datatype multiselect usará
     * para gerar o componente.
     * @example chosen, checkboxes
     */
    const MULTISELECT_RENDER_MODE = 'multiselect_render_mode';
    
    /**
     * Define se o campo será ou não visivel na tela
     * @var boolean O padrão é true
     */
    const VISIBLE = 'readonly';
    
    /**
     * Define o Label (rótulo) do campo
     * @var string
     */
    const LABEL = 'label';
    
    /**
     * Define o placeholder para o campo
     * @var string
     */
    const PLACEHOLHER = 'placeholder';
    
    /**
     * Define a url em um HTML::link <a>
     * @var string
     */
    const URL = "url";
}

