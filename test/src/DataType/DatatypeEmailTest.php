<?php
namespace MyFrameworkTest\DataType;

use MyFrameWork\DataType\DatatypeEmail;
use MyFrameWork\Enum\Flag;
use DatatypeBaseTest;

/**
 * 
 */
class DatatypeEmailTest extends DatatypeBaseTest {

    /**
     * @var DatatypeEmail
     */
    protected $email;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        parent::setUp();
        $this->email = new DatatypeEmail;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        
    }

    public function testSanitize() {
        $this->assertEquals($this->data['email'], $this->email->sanitize($this->data['email']));
        
        //Não é válido mas poderia ser um email
        $this->assertEquals('joao.souza@teste.com.br.edu.mu', $this->email->sanitize('joao.souza@teste.com.br.edu.mu'));
        $this->assertEquals('wwww.joao.souza@teste.com.br.edu.mu', $this->email->sanitize('wwww.joao.souza@teste.com.br.edu.mu'));
        
        //Invalidos
        $this->assertNull($this->email->sanitize($this->data['invalidemail']));
        $this->assertNull($this->email->sanitize($this->data['str']));
        $this->assertNull($this->email->sanitize('http://www.uol.com.br'));
    }
    
    public function testIsValid() {
        $this->assertTrue($this->email->isValid($this->data['email']));
        $this->assertTrue($this->email->isValid('joao.souza@teste.com.br.edu.mu'));
        $this->assertTrue($this->email->isValid('wwww.joao.souza@teste.com.br.edu.mu'));
        $this->assertFalse($this->email->isValid($this->data['invalidemail']));
        $this->assertFalse($this->email->isValid($this->data['str']));
        $this->assertFalse($this->email->isValid('http://www.uol.com.br'));
        
        $params = array(Flag::VALIDATE_DOMAIN);
        $this->assertFalse($this->email->isValid('joao.souza@teste.com.br.edu.mu', $params));
        $this->assertFalse($this->email->isValid('wwww.joao.souza@teste.com.br.edu.mu', $params));
        //$this->assertTrue($this->email->isValid('teste.teste@uol.com.br', $params));
    }

}
