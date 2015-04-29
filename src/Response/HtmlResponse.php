<?php

namespace MyFrameWork\Response;

use MyFrameWork\Template;
use MyFrameWork\Memory\Memory;
use MyFrameWork\Response\Response;

/* 
 * Classe response para HTML
 */
class HtmlResponse implements Response {
    
    public function setHeader() {
        header('Content-Type: text/html;charset=UTF-8');
    }

    public function renderContent($content, $file='') {
        if (!empty($file)) {
            $template = Template::singleton();
            # Memory::get('template') eh setado na Factory
            if (Memory::get('template') == 'default') {
                foreach (Memory::get('templates', array()) as $name => $mustache) {
                    $content[$name] = $template->renderTemplate($mustache, $content);
                }
            }
            $template->showRenderTemplate($file, $content);
            if (Memory::get('debug', false)) {
                var_dump($content);
            }
        }
        else {
            //TODO
            var_dump($content);
        }
    }
}

