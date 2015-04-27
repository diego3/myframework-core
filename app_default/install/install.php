<?php
function createDefaultGroups() {
    echo '<li>CreateDefaultGroups</li>';
    
    $dao = Factory::DAO('grupo');
    /* @var $dao GrupoDAO */
    $grupo = $dao->getByNome('admin');
    if (empty($grupo)) {
        assert($dao->novo('admin', 'Possui controle completo do sistema'));
    }
} createDefaultGroups();

function createDefaultUser() {
    echo '<li>CreateDefaultUser</li>';
    $dao = Factory::DAO('usuario');
    /* @var $dao UsuarioDAO */
    $email = 'admin@admin.com';
    $user = $dao->getByEmail($email);
    if (empty($user)) {
        assert($dao->novo('Administrator', $email, hashit('admin')));
        $dao = Factory::DAO('usuarioGrupo');
        /* @var $dao UsuarioGrupoDAO */
        assert($dao->novo($email, 'admin'));
    }
} createDefaultUser();