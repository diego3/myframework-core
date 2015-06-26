<?php

namespace MyFrameWork\DataType;

use MyFrameWork\DataType\DatatypeEnum;
use MyFrameWork\Enum\Flag;
use MyFrameWork\Enum\Sexo;
use MyFrameWork\Enum\Estado;

//@todo   a factory nao consegue encontrar a classe Logger...
//Warning: Uncaught require_once(D:\site\www\myframework\test/vendor/apache/log4php/src/main/php/Logger.php): 
//failed to open stream: No such file or directory

class DatatypeEnumTest extends \DatatypeBaseTest {

    /**
     * @var DatatypeEnum
     */
    protected $object;

    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        parent::setUp();
        $this->object = new DatatypeEnum();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        
    }
    
    public function testSanitize() {
        $this->assertNull($this->object->sanitize('teste'));
        
        $fsexo = array(Flag::ENUM_NAME => 'sexo');
        $this->assertNull($this->object->sanitize('teste', $fsexo));
        $this->assertEquals(Sexo::FEMININO, $this->object->sanitize(Sexo::FEMININO, $fsexo));
        $this->assertEquals(Sexo::MASCULINO, $this->object->sanitize(Sexo::MASCULINO, $fsexo));
        $this->assertNull($this->object->sanitize(Estado::PARA, $fsexo));
        
        $festado = array(Flag::ENUM_NAME => 'estado');
        $this->assertNull($this->object->sanitize('teste', $festado));
        $this->assertEquals(Estado::ACRE, $this->object->sanitize(Estado::ACRE, $festado));
        $this->assertEquals(Estado::PARA, $this->object->sanitize(Estado::PARA, $festado));
        $this->assertNull($this->object->sanitize(Sexo::MASCULINO, $festado));
    }
    
    public function testIsValid() {
        $this->assertFalse($this->object->isValid('teste'));
        
        $fsexo = array(Flag::ENUM_NAME => 'sexo');
        $this->assertFalse($this->object->isValid('teste', $fsexo));
        $this->assertTrue($this->object->isValid(Sexo::FEMININO, $fsexo));
        $this->assertTrue($this->object->isValid(Sexo::MASCULINO, $fsexo));
        $this->assertFalse($this->object->isValid(Estado::PARA, $fsexo));
        $this->assertFalse($this->object->isValid('', $fsexo));
    }

    public function testHumanValue() {
        $fsexo = array(Flag::ENUM_NAME => 'sexo');
        $this->assertEquals('Masculino', $this->object->toHumanFormat(Sexo::MASCULINO, $fsexo));
        $this->assertEquals('Feminino', $this->object->toHumanFormat(Sexo::FEMININO, $fsexo));
        $this->assertEquals('', $this->object->toHumanFormat('teste', $fsexo));
    }
}
