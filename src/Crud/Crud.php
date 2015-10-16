<?php

namespace MyFrameWork\Crud;

use MyFrameWork\Enum\Flag;
use MyFrameWork\Factory;
use MyFrameWork\Template;
use MyFrameWork\HTML;

/**
 * Classe utilitária para gerenciar o Cadastro, Edição, Alteração e Exclusão
 */
class Crud {
    /**
     * @var \MyFrameWork\DataBase\DAO
     */
    protected $dao;
    
    /**
     * Refere-se ao nome logo em seguinda do host que é
     * usado como base para as urls do crud.
     * por exemplo: http://localhost/produto/list, http://localhost/produto/edit/4
     * a url base aqui é produto
     * @var string
     */
    protected $urlbase;
    
    /**
     * Contém diversas configurações do CRUD
     * @var array
     */
    protected $config = [];

    /**
     * Dados utilizados no browse do CRUD, se vazio irá carregar dinamicamente pelo DAO
     * @var array
     */
    protected $dados = null;
    
    /**
     * Qualquer classe que implemente a ActionInterface
     * @var  \MyFrameWork\Crud\ActionInterface
     */
    protected $table_action;
    
    /**
     * Define o modo render baseado na tabela
     */
    const BROWSE_TABLE = 1;
    
    /**
     * Mode render baseado em icones lado a lado
     */
    const LADO_A_LADO  = 2;
    
    /**
     * Flag utilizada para configurar o modo de renderizar os dados
     */
    const RENDER_MODE = 'render_mode';
    
    /**
     * Exibe ou não a última coluna da tabela (exibe as ações dinamicamente)
     * @var boolean
     */
    const SHOW_TABLE_ACTIONS = 'show_table_actions';
    
    /**
     * Define a URL para edição ou exclusão
     * @var string
     */
    const ACTION_URL_EDIT = 'action_url_edit';
    const ACTION_URL_DELETE = 'action_url_delete';
    
    /**
     * Define qual método será utilizado na operação de INSERT do DAO
     */
    const INSERT_METHOD = 'insert';
    
    /**
     * Define qual método será utilizado na operação de UPDATE do DAO
     */
    const UPDATE_METHOD = 'update';
    
    /**
     * Permite adicionar mais botões no formulário de edição dos dados do crud
     * 
     * O value do method Crud::setConfig deve ser uma array como no exemplo
     * 
     * $buttons = [
     *       "url" => 'page/action',
     *       "class" => 'btn btn-cancel',
     *       "label" => 'Cancelar'
     *   ];
     * 
     * também pode ser uma array bi-dimensional no caso de mais de um button
     * 
     * $buttons = [
     *   [
     *       "url" => 'page/action',
     *       "class" => 'btn btn-cancel',
     *       "label" => 'Cancelar'
     *   ],
     *   [
     *       "url" => 'page/action',
     *       "class" => 'btn btn-success',
     *       "label" => 'Confirmar'
     *   ],
     * ];
     */
    const EDIT_BUTTONS  = 'edit_buttons';
    
    /**
     * O id processado no caso de successo ou -1 para falha
     */
    const SAVE_RETURN_INT = 1;
    /**
     * Boolean em caso de sucesso ou falha ou a mensagem de erro lançada na PDOException
     */
    const SAVE_RETURN_BOOL = true;
    
    /**
     *
     * o tipo de retorno do metodo save, se é bool ou int.
     * use Crud::SAVE_RETURN_INT  para configurar o retorno para inteiro
     * use Crud::SAVE_RETURN_BOOL para configurar o retorno para boolean 
     */
    const SAVE_RETURN_TYPE = 'save_return_type'; 
    
    /**
     * Configuração dos parâmetros do método save
     */
    const SAVE_METHOD_PARAMS = 'save_method_params';
    
    /**
     * Define a URL para salvar e editar um formulário
     * @var string
     */
    const ACTION_URL_SAVE = 'action_url_save';
    const ACTION_URL_NEWBUTTON = 'action_url_newbutton';
    
    const ORDER_BY = 'order_by';
    
    /**
     * Cria um objeto CRUD
     * @param string $urlbase URL base da página
     * @param string $daoname Nome do DAO Associado
     */
    public function __construct($urlbase, $daoname='') {
        $this->urlbase = $urlbase;
        if (!endsWith($urlbase, '/')) {
            $this->urlbase .= '/';
        }
        if (empty($daoname)) {
            $daoname = str_replace('/', '', $urlbase);
        }
        $this->dao = Factory::DAO($daoname);
    }
    
    /**
     * Monta os dados do formulário para inserir ou editar um registro 
     * 
     * @param  int   $id         O id do page
     * @param  array $formdata   As informações fornecidas pelo addParameter
     * @param  array $formvalues Valores de todos os campos do formulário já validados
     * @return array
     */
    public function edit($id, $formdata, $formvalues) {
        if (empty($id)) {
            //Inserir
            $action = $this->urlbase . getValueFromArray($this->config, self::ACTION_URL_SAVE, 'save');
        }
        else {
            //Alterar
            $action = $this->urlbase . getValueFromArray($this->config, self::ACTION_URL_SAVE,  'save/'. $id);
            $result = $this->dao->getById($id);
            $formvalues = $result;
            //$formvalues = array_merge($result, $formvalues); 
        }
        
        $r = [
            'action' => $action,
            'class'  => 'form-horizontal crud',
            'method' => 'post',
            'hidden' => [],
            'formgroup' => []
        ];
        
        foreach ($formdata as $fieldname => $params) {            
            if (getValueFromArray($params['params'], Flag::VISIBLE, true)) {
                $r['formgroup'][] = [
                    'formitem' => [
                        'id'       => $fieldname . '_id',
                        'label'    => getValueFromArray($params['params'], Flag::LABEL, ''),
                        'item'     => $this->getDataType($params, $fieldname, $formvalues),
                        'required' => in_array(Flag::REQUIRED, $params['params']) || getValueFromArray($params['params'], Flag::REQUIRED, false),
                        'error'    => getValueFromArray($params, 'error'),
                        'ischeckbox' => $params['type'] == 'bool'
                    ]
                ];
            }
            else {
                $r['hidden'][] = ['name' => $fieldname, 'value' => getValueFromArray($formvalues, $fieldname, '')];
            }
        }
        
        $buttons = getValueFromArray($this->config, static::EDIT_BUTTONS, false);
        if($buttons) {
            $r["buttons"] = $buttons;
        }
        return $r;
    }
    
    protected function getDataType($params, $fieldname, $formvalues) {
        $datatype = Factory::datatype($params['type'])
                            ->getHTMLEditable($fieldname, getValueFromArray($formvalues, $fieldname, ''), $params['params']);
        if(null === $datatype or empty($datatype)) {
            //tentar criar um datatype da aplicação
            //$datatype = \Application\Model\DataType\DataTypeFactory::create($params["type"]);
            
        }
        return $datatype;
    }
    
    /**
     * Executa um INSERT ou UPDATE no DAO do Crud atual.
     * 
     * Por padrão o Crud usa o método insert e update do DAO. 
     * É possível alterar esses métodos utilizando Crud::INSERT_METHOD => 'methodName' e Crud::UPDATE_METHOD usando o Crud::setConfig
     * 
     * @note   Caso você usa Crud::UPDATE_METHOD não esqueça que o Crud SEMPRE vai jogar o campo ID
     *         no ULTIMO parâmetro do seu método!
     *         exemplo: seu método deve ter a seguinte assinatura:  MeuDAO::atualiza($campo, $ID); não esqueça! coloque o ID sempre no ULTIMO parâmetro!
     * 
     * @param  int    $id        O identificador da tabela
     * @param  array $values     Os valores depois do processo de limpeza e validação
     * @return mixed          
     */
    public function save($id, array $values) {
        if (empty($id)) {
            //INSERT
            $method = getValueFromArray($this->config, static::INSERT_METHOD, 'insert') ;
            if($method !== 'insert') {
                $parametros = getValueFromArray($this->config, static::SAVE_METHOD_PARAMS, array_values($values));
                $retorno = call_user_func_array([ $this->dao, $method ], $parametros);
            }
            else if($method == 'insert') {
                $retorno = $this->dao->{$method}($values);
            }
        }
        else {
            //UPDATE
            $method = getValueFromArray($this->config, static::UPDATE_METHOD, 'update');
            if($method !== 'update') {
                $parametros = getValueFromArray($this->config, static::SAVE_METHOD_PARAMS, array_values($values));
                //coloca o id no ultimo parametro do metodo que o desenvolvedor configurou
                array_push($parametros, $id);
                //chama o metodo dinamicamente
                $retorno = call_user_func_array([ $this->dao, $method ], $parametros);
            }
            else if($method == 'update') {
                $retorno =  $this->dao->{$method}($values, $id);
            }
        }
        
        $return_type = getValueFromArray($this->config, static::SAVE_RETURN_TYPE, true);
        if(is_int($return_type)) {
            if ($retorno) {
                return $id;
            }
            return -1;
        }
        else if(is_bool($return_type)) {
            return $retorno;
        }
    }
    
    /**
     * Define os dados para uma chamada browse.
     * Geralmente qualquer retorno de tipo fetch do PDO, ou seja,
     * você pode usar qualquer retorno de um DAO.
     * Geralmente esse método é utilizado para popular a tabela com alguma
     * consulta mais elaborada, sendo assim, sobrescrevendo o comportamento 
     * padrão do crud, que usa o DAO::listAll
     * 
     * @param array $dados
     */
    public function setDados(array $dados) {
        $this->dados = $dados;
    }
    
    /**
     * Define uma configuração específica para o CRUD
     * 
     * @param Crud::CONST $param   Use sempre as constantes
     * @param mixed $value         O valor da configuração
     * @return \MyFrameWork\Crud
     */
    public function setConfig($param, $value) {
        $this->config[$param] = $value;
        return $this;
    }
    
    /**
     * Recupera o array de configurações do crud
     * 
     * @return array
     */
    public function getConfig() {
        return $this->config;
    }
    
    /**
     * Monta um componente para exibir os dado. Esse componente eh baseado
     * em um modo de renderização passado em parâmetro.
     * 
     * @param string $title  Um simples título
     * @param array $schema  O esquema utilizado na renderização da view
     * @param int $mode      Modo de renderização da view por padrão é uma tabela
     * @return array
     */
    public function browse($title, $schema, $mode = self::BROWSE_TABLE) {
        if (isset($this->dados)) {
            $dados = $this->dados;
        }
        else {
            $dados = $this->dados = $this->dao->listAll(getValueFromArray($this->config, Crud::ORDER_BY, array()));
        }
        
        //definido o modo render para a listagem dos dados
        $pagedata = [];
        switch($mode) {
            case self::LADO_A_LADO :
                $pagedata['ladoalado'] = $this->getLadoALado($dados, $schema);
                break;
            default:
                $pagedata['tabledata'] = $this->getTable($dados, $schema);
        }
        
        $pagedata['title'] = 'Lista de ' . ucfirst(str_replace("/", "",$title)) . "s" ;
        $pagedata['breadcrumb'] = ['items' => 
            [
                ['url' => 'main/dashboard', 'label' => 'Home'],
                ['label' => $title ]
            ]
        ];
        $pagedata['buttons'] = array(
            array(
                'class' => 'btn btn-primary btn-success',
                'label' => 'Novo Registro',
                'url' => getValueFromArray($this->config, static::ACTION_URL_NEWBUTTON, $this->urlbase . 'edit')
            )
        );
        return $pagedata;
    }
    
    /**
     * Configura os dados para o modo render com icones lado a lado
     * 
     * @param  array  $dados
     * @param  array  $schema
     * @return string
     */
    public function getLadoALado($dados, $schema) {
        $pagedata = [];
        
        $action = getValueFromArray($this->config, Crud::SHOW_TABLE_ACTIONS, true);
        
        if ($action) {
            //@todo refatorar  #init
            $dao_pks = $this->dao->getPKFieldName();
            if(is_string($dao_pks)) {
                $pk = $dao_pks;
            }
            else if (is_array($dao_pks)) {
                $pk = isset($dao_pks[0]) ? $dao_pks[0] : "no-primary-key-setted";
            }
            else {
                $pk = "id";//daoname_id
            }
            
            $default_edit_url   = str_replace('<id>', $pk, $this->urlbase . 'edit/{{<id>}}');
            $default_delete_url = str_replace('<id>', $pk, $this->urlbase . 'delete/{{<id>}}');
            
            $urledit   = getValueFromArray($this->config, Crud::ACTION_URL_EDIT,   $default_edit_url  );
            $urldelete = getValueFromArray($this->config, Crud::ACTION_URL_DELETE, $default_delete_url);
            # refatorar end
        }
        
        $m = Template::singleton();
        
        foreach($dados as $row) {
            $item = [];
            foreach($schema as $key => $template) {
                $item[$key] = $m->renderHTML($template, $row);
            }
            
            if ($action) {
                $actions = HTML::link(
                    $m->renderHTML($urledit, $row),
                    '<span class="glyphicon glyphicon-pencil"></span>',
                    'Editar este item',
                    ['class' => 'btn btn-default btn-xs']
                );
                
                $actions .= ' ' . $this->getUserActions($m, $row);

                $actions .= ' ' . HTML::link(
                    $m->renderHTML($urldelete, $row),
                    '<span class="glyphicon glyphicon-trash"></span>',
                    'Excluir este item',
                    ['class' => 'btn btn-danger btn-xs']
                );
                
                $item["actions"] = $actions;
            }
            
            $pagedata["item"][] = $item;
        }
        
        return $pagedata;
    }
    
       
    
    /**
     * Configura os dados para o modo render em tabela
     * 
     * @param array $dados
     * @param array $schema
     * @return string
     */
    public function getTable($dados, $schema) {
        $thead = array_keys($schema);
        $action = getValueFromArray($this->config, Crud::SHOW_TABLE_ACTIONS, true);
        
        if ($action) {
            $thead[]   = 'Ações';
            
            #@todo refatorar init
            $dao_pks = $this->dao->getPKFieldName();
            if(is_string($dao_pks)) {
                $pk = $dao_pks;
            }
            else if (is_array($dao_pks)) {
                $pk = isset($dao_pks[0]) ? $dao_pks[0] : "no-primary-key-setted";
            }
            else {
                $pk = "id";//daoname_id
            }
            
            $default_edit_url   = str_replace('<id>', $pk, $this->urlbase . 'edit/{{<id>}}');
            $default_delete_url = str_replace('<id>', $pk, $this->urlbase . 'delete/{{<id>}}');
            
            $urledit   = getValueFromArray($this->config, Crud::ACTION_URL_EDIT,   $default_edit_url  );
            $urldelete = getValueFromArray($this->config, Crud::ACTION_URL_DELETE, $default_delete_url);
            #refatorar end
        }
        
        $r = [
            'tfoot' => count($dados) . ' registro(s)',
            'thead' => $thead,
            'tbody' => [],
            'class' => 'table-striped table-hover table-bordered browsetable'
        ];
        
        $t = Template::singleton();
        foreach ($dados as $row) {
            $td = [];
            foreach ($schema as $template) {
                $td[] = $t->renderHTML($template, $row);
            }
            if ($action) {
                $actions = HTML::link(
                    $t->renderHTML($urledit, $row),//href
                    '<span class="glyphicon glyphicon-pencil"></span>',//label
                    'Editar este item',//titulo
                    ['class' => 'btn btn-default btn-xs']//atributos extras 
                );
                
                $actions .= ' ' . $this->getUserActions($t, $row);

                $actions .= ' ' . HTML::link(
                    $t->renderHTML($urldelete, $row),
                    '<span class="glyphicon glyphicon-trash"></span>',
                    'Excluir este item',
                    ['class' => 'btn btn-danger  btn-xs confirmacao']
                );
                
                $td[] = $actions;
            }
            $r['tbody'][] = $td;
        }
        return $r;
    }
    
    /**
     * 
     * @param \MyFrameWork\Crud\ActionInterface $action
     * @return \MyFrameWork\Crud
     */
    public function setTableAction(\MyFrameWork\Crud\ActionInterface $action) {
        $this->table_action = $action;
        return $this;
    }
    
    /**
     * Retorna as actions que o usuário definiu para a última tabela
     * 
     * @param  Mustache_Engine $mustache Instância do mustache
     * @param  array           $row      Linha de dados do banco de dados
     * @return string                    Retorna o html da(s) action(s)
     */
    protected function getUserActions($mustache, $row) {
        if(null !== $this->table_action) {
            return $this->table_action->getActions($mustache, $row);
        }
        return "";
    }
    
    /**
     * 
     * @param int $id
     * @return mixed Boolean em caso de sucesso ou falha na operação ou a mensagem da PDOException caso seja lançada
     */
    public function delete($id) {
        return $this->dao->delete($id);
    }
    
    /**
     * Retorna o DAO gerenciado pelo crud
     * @return \MyFrameWork\DataBase\DAO
     */
    public function getDao() {
        return $this->dao;
    }
    
    /**
     * 
     * @return string
     */
    public function getUrlbase() {
        return $this->urlbase;
    }
    
    /**
     * 
     * @param string $url
     * @return \MyFrameWork\Crud\Crud
     */
    public function setUrlbase($url) {
        if(!endsWith($url, "/")) {
            $url .= '/';
        }
        $this->urlbase = $url;
        return $this;
    }
}
