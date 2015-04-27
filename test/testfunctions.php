<?php

/*
 * Funções gerais utilizadas nos testes
 */

/**
 * Cria um novo usuário
 * @return array
 */
function createRandomUser() {
    $name = 'Name' . rand(1, 100000);
    $email = 'emailuser' . $name . '@test.com';
    $dao = Factory::DAO('usuario');
    /* @var $dao UsuarioDAO */
    $dao->novo($name, $email, hashit($email));
    return $dao->getByEmail($email);
}

/**
 * Cria um novo grupo
 * @return array
 */
function createRandomGroup() {
    $name = 'Group' . rand(1, 100000);
    $dao = Factory::DAO('grupo');
    $dao->novo($name, 'Description of ' . $name);
    return $dao->getByNome($name);
}
