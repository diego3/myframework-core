<?php

class GrupoDAO extends DAO {
    protected function setParams() {
        $this->tablename = 'grupo';
    }
    
    /**
     * Cria um novo grupo
     * @param string $grupo Nome do grupo
     * @param string $description Descrição do grupo
     * @return int
     */
    public function novo($grupo, $description='') {
        return $this->insert(array('nome' => $grupo, 'descricao' => $description));
    }
    
    /**
     * Retorna os dados de grupo pelo seu nome
     * @param string $grupo Nome do grupo
     */
    public function getByNome($grupo) {
        return $this->getByKey('nome', $grupo);
    }
    
    /**
     * @return UsuarioGrupoDAO
     */
    public function UsuarioGrupo() {
        return Factory::DAO('usuarioGrupo', $this->getDatabase());
    }
}
