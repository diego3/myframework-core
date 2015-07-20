<?php

namespace MyFrameWork\Email;

use MyFrameWork\Enum\BasicEnum;

/**
 * Lista de tipos de Emails disponíveis
 *
 * @author Diego Rosa dos Santos <diegosantos@alphaeditora.com.br>
 */
abstract class Emails extends BasicEnum {
    
    /**
     * Email padrão
     */
    const EMAIL = "\MyFrameWork\Email\Email";
    /**
     * Email Html
     */
    const HTML = "\MyFrameWork\Email\EmailHtml";
    
    //outros
    
    
    
}
