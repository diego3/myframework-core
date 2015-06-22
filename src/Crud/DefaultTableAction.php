<?php

namespace MyFrameWork\Crud;

use MyFrameWork\Crud\ActionInterface;
use MyFrameWork\HTML;

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
     * @param string $label_text  Descrição da ação, o texto do link
     * @param array $extra_attr   Um array com atribulos html extra onde a chave é o tipo do atributo e o value é o seu valor
     */
    public function add($url, $icon_class, $title, $label_text = "" ,array $extra_attr = []) {
        $this->actions[] = [
            "url"        => $url,
            "icon_class" => $icon_class,
            "title"      => $title,
            "label_text" => $label_text,
            "extra"      => $extra_attr
        ];
    }
    
    /**
     * Uma action nesse contexto representa o link que executa uma ação específica
     * em um grid, específicamente na última coluna do grid, na coluna AÇÕES.
     * 
     * @param Mustache_Engine $mustache
     * @param array $row
     */
    public function getActions($mustache, $row) {
        $actions = "";
        foreach($this->actions as $config) {
            
            $actions .= "  " . HTML::link(
                $mustache->renderHTML($config['url'], $row), 
                '<span class="' . $config['icon_class']. '"></span> ' . $config['label_text'] . '  ',
                $config['title'],
                $config['extra']
            ); 
        }
        return $actions;
    }
    
}
