<?php

namespace MyFrameWork\Response;

/* 
 * Interface Response para geradores de saída
 */

interface Response {
    /**
     * Define o cabeçalho HTTP
     */
    public function setHeader();
    
    /**
     * Renderiza o conteúdo conforme o tipo de resposta
     * @param array $content Dados do conteúdo que deverá ser renderizado
     * @param string $file [Opicional] Para tipos que utilizam templates o nome do arquivo de template
     */
    public function renderContent($content, $file='');
}