<?php

class StaticPageDAO extends DAO {
    protected function setParams() {
        $this->tablename = 'staticpage';
        $this->hasactive = false;
    }
    
    /**
     * Cria uma nova página estática
     * @return int
     */
    public function novo($nome, $titulo, $conteudo) {
        return $this->insert(array('nome' => $nome, 'titulo' => $titulo, 'conteudo' => $conteudo));
    }
    
    /**
     * Retorna os dados da página pelo seu nome
     * @param string $nome Nome da página
     */
    public function getByNome($nome) {
        return $this->getByKey('nome', $nome);
    }
}
