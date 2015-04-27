<?php

class LogBuscaDAO extends DAO {
    
    protected function setParams() {
        $this->tablename = 'logbusca';
        $this->hasactive = false;
    }
    
    /**
     * Cria um novo resultado de busca
     * @param string $termo Termo que foi pesquisado
     * @param int $total Quantos resultados foram encontrados ao realizar a busca
     * @param string $query Consulta realizada no banco de dados
     * @param string $sessionid Hash da sessão do usuário (somente utilizado quando o usuario não estiver logado)
     * @param int $usuario Codigo do usuario logado no sistema (se o mesmo estiver logado)
     * @return int
     */
    public function novo($termo, $total, $query, $sessionid, $usuario=null) {
        if (empty($sessionid)) {
            Factory::log()->info('Sessionid e usuário são vazios');
            return 0;
        }
        return $this->insert(array(
            'termo' => $termo,
            'totalresultado' => $total,
            'query' => $query,
            'sessionid' => $sessionid,
            'usuario' => $usuario
        ));
    }
    
    /**
     * Retorna o código da ultima busca (para a sessão ou usuário)
     * @param string $grupo Nome do grupo
     */
    public function getLastId($sessionid, $usuario) {
        if (is_null($usuario)) {
            //Buscando o id da ultima busca (usuario nao logado)
            $sql = 'SELECT id FROM logbusca WHERE sessionid = ? ORDER BY id DESC LIMIT 1';
            return $this->db->fetchField($sql, array($sessionid));
        }
        else {
            //Buscando o id da ultima busca (usuario logado)
            $sql = 'SELECT id FROM logbusca WHERE sessionid = ? or usuario = ? ORDER BY id DESC LIMIT 1';
            return $this->db->fetchField($sql, array($sessionid, $usuario));
        }
        
    }
}
