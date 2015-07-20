<?php

namespace MyFrameWork\Email;

use MyFrameWork\Enum\BasicEnum;

/**
 * Description of Mailers
 *
 * @author Diego Rosa dos Santos <diegosantos@alphaeditora.com.br>
 */
abstract class Mailers extends BasicEnum {
    //identificator = full class namespace
    
    const PHP = "\MyFrameWork\Email\PhpMailer";
    
    
}
