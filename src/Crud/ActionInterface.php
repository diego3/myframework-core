<?php

namespace MyFrameWork\Crud;

/**
 * Description of ActionInterface
 *
 * @author Diego
 */
interface ActionInterface {
    
    /**
     * Uma action nesse contexto representa o link que executa uma ação específica
     * em um grid, específicamente na última coluna do grid, na coluna AÇÕES.
     * @param Mustache_Engine $mustache
     * @param array $row
     */
    public function getActions($mustache, $row);
    
}
