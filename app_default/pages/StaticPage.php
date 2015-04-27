<?php
require_once 'util/Crud.php';

class StaticPage extends ProcessRequest {
    /**
     * @var Crud
     */
    protected $crud;
    
    public function __construct() {
        $this->allowMethod('browse', array('admin'));
        $this->allowMethod('edit', array('admin'));
        $this->allowMethod('delete', array('admin'));
        $this->crud = new Crud($this->getPageName(), 'staticPage'); 
    }
    
    public function _main() {
        if (empty($this->method) && !Session::singleton()->isAdmin()) {
            redirect('/');
        } else if (!empty($this->method)) {
            $this->showPage();
        }
        else {
            $this->_browse();
        }
        return true;
    }
    
    public function showPage() {
        $dao = Factory::DAO('StaticPage');
        $page = $dao->getByNome($this->method);
        if (empty($page)) {
            redirect('/');
        }
        $this->pageTitle = $page['titulo'];
        $this->pagedata['nome'] = $page['nome'];
        $this->pagedata['titulo'] = $page['titulo'];
        $this->pagedata['conteudo'] = Factory::datatype('html')->toHumanFormat($page['conteudo']);
        if (empty($this->pageTitle)) {
            $this->pageTitle = $this->pagedata['titulo'];
        }
    }
    
    protected function setParameters() {
        $this->addParameter(
            'nome', 'string', 
            array(Flag::MAXLENGHT => 30, Flag::REQUIRED, Flag::LABEL => 'Nome', Flag::PLACEHOLHER => 'Nome utilizado na URL (sem espaços)', Flag::ALNUM)
        );
        $this->addParameter(
            'titulo', 'string', 
            array(Flag::MAXLENGHT => 100, Flag::REQUIRED, Flag::LABEL => 'Titulo', Flag::PLACEHOLHER => 'Título da página')
        );
        $this->addParameter(
            'conteudo', 'html',
             array(Flag::REQUIRED, Flag::LABEL => 'Conteúdo')
        );
    }
    
    /**
     * Lista as empresas
     */
    public function _browse() {
        $table = array(
            'Nome' => '{{nome}}',
            'Título' => '{{titulo}}',
        );
        $this->pagedata = $this->crud->browse('Páginas', $table);
        $this->setTemplateFile('crud_listar');
        return true;
    }
    
    public function _edit($setAdmin=true) {
        if ($setAdmin) {
            $this->setParameters();
        }
        else {
            $this->pagedata['formerro'] = 'Falha ao salvar o registro no banco de dados';
        }
        
        $this->setTemplateFile('crud_editar');
        $this->pagedata['titulo'] = empty($this->id) ? 'Nova página' : 'Alterar dados da página';
        $this->pagedata['form'] = $this->crud->edit($this->id, $this->parametersMeta[$this->getMethod()], $this->parametersValue);
        return true;
    }
    
    public function _save() {
        $this->setParameters();
        $this->cleanParameters();
        if ($this->isParametersValid()) {
            $id = $this->crud->save($this->id, $this->parametersValue);
            if ($id > 0) {
                redirect($this->getPageName() . '/' . $id . '/edit');
                return 0;
            }
        }
        return $this->_edit(false);
    }
    
    public function _delete() {
        $this->setAdminPage();
        $this->crud->delete($this->id);
        redirect($this->getPageName() . '/browse');
    }
}