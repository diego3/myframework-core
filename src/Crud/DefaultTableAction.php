<?php

namespace MyFrameWork\Crud;

use MyFrameWork\Crud\ActionInterface;

/**
 * Description of DefaultTableAction
 *
 * @author Diego
 */
class DefaultTableAction implements ActionInterface {
    
    protected $actions = [];
    
    /**
     * 
     * @param string $url         A rota na qual o ação será encaminhada 
     * @param string $icon_class  Somente a classe CSS do ícone
     * @param string $title       Um texto de ajuda que será exibido como toolip
     * @param array $extra_attr   Um array com atribulos html extra onde a chave é o tipo do atributo e o value é o seu valor
     */
    public function add($url, $icon_class, $title, array $extra_attr = []) {
        $this->actions[] = [
            "url" => $url,
            "icon_class" => $icon_class,
            "title" => $title,
            "extra" => $extra_attr
        ];
    }
    
    /**
     * Uma action nesse contexto representa o link que executa uma ação específica
     * em um grid, específicamente na última coluna do grid, na coluna AÇÕES.
     * @param Mustache_Engine $mustache
     * @param array $row
     */
    public function getActions($mustache, $row) {
        $actions = "";
        foreach($this->actions as $config) {
            $actions .= " " . HTML::link(
                $mustache->renderHTML($config['url'], $row), 
                '<span class="' . $config['icon_class']. '"></span>',
                $config['title'],
                $config['extra']
            ); 
        }
        return $actions;
    }
    
}
