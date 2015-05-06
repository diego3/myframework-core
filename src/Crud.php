<?php

namespace MyFrameWork;
use MyFrameWork\Enum\Flag;
use MyFrameWork\Factory;
use MyFrameWork\Template;
use MyFrameWork\HTML;

/**
 * Classe utilitária para gerenciar o Cadastro, Edição, Alteração e Exclusão
 */
class Crud {
    /**
     * @var DAO
     */
    protected $dao;
    
    /**
     * URL base
     * @var string
     */
    protected $urlbase;
    
    /**
     * Contém diversas configurações do CRUD
     * @var array
     */
    protected $config = array();

    /**
     * Dados utilizados no browse do CRUD, se vazio irá carregar dinamicamente pelo DAO
     * @var array
     */
    protected $dados = null;
    
    /**
     * Define o modo render baseado na tabela
     */
    const BROWSE_TABLE = 1;
    
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
     * 
     * @param int $id
     * @param array $formdata As informações fornecidas pelo addParameter
     * @param array $formvalues Valores de todos os campos do formulário já validados
     * @return array
     */
    public function edit($id, $formdata, $formvalues) {
        if (empty($id)) {
            //Inserir
            $action = $this->urlbase . getValueFromArray($this->config, self::ACTION_URL_SAVE, 'save');
        }
        else {
            //Alterar
            $action = $this->urlbase . getValueFromArray($this->config, self::ACTION_URL_SAVE,  $id . '/save');
            $formvalues = array_merge($this->dao->getById($id), $formvalues); 
        }
        $r = array(
            'action' => $action,
            'class' => 'form-horizontal crud',
            'method' => 'post',
            'hidden' => array(),
            'formgroup' => array()
        );
        foreach ($formdata as $fieldname => $params) {            
            if (getValueFromArray($params['params'], Flag::VISIBLE, true)) {
                $r['formgroup'][] = array(
                    'formitem' => array(
                        'id' => $fieldname . '_id',
                        'label' => getValueFromArray($params['params'], Flag::LABEL, ''),
                        'item' => Factory::datatype($params['type'])->getHTMLEditable($fieldname, getValueFromArray($formvalues, $fieldname, ''), $params['params']),
                        'required' => in_array(Flag::REQUIRED, $params['params']) || getValueFromArray($params['params'], Flag::REQUIRED, false),
                        'error' => getValueFromArray($params, 'error')
                    )
                );
            }
            else {
                $r['hidden'][] = array('name' => $fieldname, 'value' => getValueFromArray($formvalues, $fieldname, ''));
            }
        }
        return $r;
    }
    
    /**
     * 
     * @param int $id
     * @param array $values
     * @return int O id processado no caso de successo ou -1 para falha
     */
    public function save($id, $values) {
        if (empty($id)) {
            //INSERT
            if ($this->dao->insert($values)) {
                return $this->dao->getLastId();
            }
            return -1;
        }
        else {
            //UPDATE
            if (!$this->dao->update($values, $id)) {
                return -1;
            }
        }
        return $id;
    }
    
    /**
     * Define os dados para uma chamada browse
     * @param array $dados
     */
    public function setDados($dados) {
        $this->dados = $dados;
    }
    
    /**
     * Define uma configuração específica para o CRUD
     * @param Crud::CONST $param Use sempre 
     * @param mixed $value O valor da configuração
     * @return Crud
     */
    public function setConfig($param, $value) {
        $this->config[$param] = $value;
        return $this;
    }
    
    /**
     * 
     * @param string $title
     * @param array $schema
     * @param int $mode
     * @return array
     */
    public function browse($title, $schema, $mode=self::BROWSE_TABLE) {
        if (isset($this->dados)) {
            $dados = $this->dados;
        }
        else {
            $dados = $this->dados = $this->dao->listAll(getValueFromArray($this->config, Crud::ORDER_BY, array()));
        }
        $pagedata = array();
        switch($mode) {
            default:
                $pagedata['tabledata'] = $this->getTable($dados, $schema);
        }
        $pagedata['title'] = 'Lista de ' . $title;
        $pagedata['breadcrumb'] = array(
            'items' => array(
                array('url' => 'main/dashboard', 'label' => 'Home'),
                array('label' => $title)
            )
        );
        $pagedata['buttons'] = array(
            array(
                'class' => 'btn btn-primary',
                'label' => 'Novo Registro',
                'url' => $this->urlbase . getValueFromArray($this->config, self::ACTION_URL_NEWBUTTON, 'edit')
            )
        );
        return $pagedata;
    }
    
    /**
     * 
     * @param array $dados
     * @param array $schema
     * @return string
     */
    public function getTable($dados, $schema) {
        $thead = array_keys($schema);
        $action = getValueFromArray($this->config, Crud::SHOW_TABLE_ACTIONS, true);
        if ($action) {
            $thead[] = '#';
            $urledit = getValueFromArray($this->config, Crud::ACTION_URL_EDIT, $this->urlbase . '{{id}}/edit');
            $urldelete = getValueFromArray($this->config, Crud::ACTION_URL_DELETE, $this->urlbase . '{{id}}/delete');
        }
        $r = array(
            'tfoot' => count($dados) . ' registro(s)',
            'thead' => $thead,
            'tbody' => array(),
            'class' => 'table-striped table-hover table-bordered browsetable'
        );
        $t = Template::singleton();
        foreach ($dados as $row) {
            $td = array();
            foreach ($schema as $template) {
                $td[] = $t->renderHTML($template, $row);
            }
            if ($action) {
                $td[] = HTML::link(
                    $t->renderHTML($urledit, $row),
                    '<span class="glyphicon glyphicon-pencil"></span>',
                    'Editar este item',
                    array('class' => 'btn btn-default btn-xs')
                ) . ' ' . HTML::link(
                    $t->renderHTML($urldelete, $row),
                    '<span class="glyphicon glyphicon-trash"></span>',
                    'Excluir este item',
                    array('class' => 'btn btn-danger  btn-xs confirmacao')
                );
            }
            $r['tbody'][] = $td;
        }
        return $r;
    }
    
    /**
     * 
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        return $this->dao->delete($id);
    }
}
