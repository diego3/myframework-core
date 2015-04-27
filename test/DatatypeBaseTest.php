<?php
require_once '/datatype/datatype.php';

class DatatypeBaseTest extends PHPUnit_Framework_TestCase {
    /**
     * @var array
     */
    protected $data;

    protected function setUp() {
        $this->data = array();
        $this->data['str'] = 'Hello World!';
        $this->data['html'] = '<p>Hello World</p>';
        $this->data['text'] = <<<TEXT
Hello World!
    I'm fine, thanks and you?
  Do you remember that "this is nice, very nice!"
Bye.           


TEXT;
        
        $this->data['code'] = <<<CODE
<?php
   var_dump("teste");
?>
CODE;
        
        $this->data['stringint'] = '  1234   ';
        $this->data['negativeint'] = '-1234';
        $this->data['int'] = 1234;
        $this->data['double'] = 1234.1234;
        $this->data['email'] = 'joaao@uol.com.br';
        $this->data['invalidemail'] = 'teste.com.br@joaao@uol.com.br';
     }

}
