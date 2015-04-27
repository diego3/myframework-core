<?php


class LogacessoDAO extends DAO {
    
    protected function setParams() {
        $this->tablename = 'logacesso';
        $this->hasactive = false;
    }
    
    /**
     * Cria um novo log de acesso
     */
    public function novo($sessionid, $ip, $reverso, $navigator) {
        if (empty($sessionid)) {
            Factory::log()->info('Sessionid e usuário são vazios');
            return 0;
        }
        return $this->insert(array(
            'sessionid' => $sessionid,
            'ip' => $ip,
            'ipreverso' => $reverso,
            'navigatorso' => $navigator
        ));
    }
}
