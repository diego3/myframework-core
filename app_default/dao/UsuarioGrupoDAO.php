<?php

class UsuarioGrupoDAO extends DAO {
    
    protected function setParams() {
        $this->tablename = 'usuariogrupo';
        $this->hasactive = false;
    }
    
    /**
     * Define um ou mais grupos para o usuário
     * @param string|int $usuario Email ou id do usuário
     * @param mixed $grupos Vetor de ids ou emails ou apenas um id ou email
     * @return int Número de grupos inseridos
     */
    public function novo($usuario, $grupos) {
        $usuario = $this->getUsuarioId($usuario);
        if ($usuario < 0) {
            Factory::log()->warn('Usuário inválido');
            return 0;
        }
        if (!is_array($grupos)) {
            $grupos = array($grupos);
        }
        
        $total = 0;
        foreach ($grupos as $grupo) {
            $grupo = $this->getGrupoId($grupo);
            if ($grupo < 0) {
                continue;
            }
            $total += $this->insert(array('usuario' => $usuario, 'grupo' => $grupo));
        }
        return $total;
    }
    
    /**
     * Exclui todos os grupos de um usuário
     * @param string|int $usuario Email ou id do usuário
     * @return int Número de linhas excluídas
     */
    public function deleteByUsuario($usuario) {
        return $this->db->delete($this->getTableName(), array('usuario' => $this->getUsuarioId($usuario)));
    }
    
    /**
     * Retorna todos os grupos de um usuário
     * @param string|int $usuario Id ou E-mail do usuário
     * @return array
     */
    public function listByUsuario($usuario, $campos='grupo.*') {
        $tables = array('usuariogrupo' => '', 'grupo' => 'usuariogrupo.grupo = grupo.id');
        return $this->_getIndexedArray($tables, 'grupo', $campos, array('usuario' => $this->getUsuarioId($usuario)));
    }
    
    /**
     * Retorna todos os usuários de um grupo
     * @param string|int $grupo Id ou Nome do grupo
     * @return array
     */
    public function listByGrupo($grupo, $campos='usuario.*') {
        $tables = array('usuariogrupo' => '', 'usuario' => 'usuariogrupo.usuario = usuario.id');
        return $this->_getIndexedArray($tables, 'usuario', $campos, array('grupo' => $this->getGrupoId($grupo)));
    }
    
    /**
     * Sempre retorna o Id do usuário
     * @param string|int $usuario Email ou id do usuário
     * @return int Id do usuário, se o mesmo não for encontrado retorna -1
     */
    protected function getUsuarioId($usuario) {
        return $this->loadDAO('usuario')->getTableId('email', $usuario);
    }
    
    /**
     * Sempre retorna o Id do grupo
     * @param string|int $grupo Nome ou id do grupo
     * @return int Id do grupo, se o mesmo não for encontrado retorna -1
     */
    protected function getGrupoId($grupo) {
        return $this->loadDAO('grupo')->getTableId('nome', $grupo);
    }
}