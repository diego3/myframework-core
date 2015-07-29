<?php

namespace MyFrameWork\DataType;

use MyFrameWork\DataType\DatatypeString;
use MyFrameWork\Enum\Flag;
use MyFrameWork\Memory\Memory;

//@todo a Factory nao consegue encontrar o Logger.php
//Warning: Uncaught require_once(D:\site\www\myframework\test/vendor/apache/log4php/src/main/php/Logger.php): 
//failed to open stream: No such file or directory


class DatatypeStringTest extends DatatypeBaseTest {

    /**
     * @var DatatypeString
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        parent::setUp();
        $this->object = new DatatypeString;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
    }
    
    public function testSanitize() {
        $this->assertEquals($this->data['str'], $this->object->sanitize($this->data['str']));
        $this->assertEquals(strip_tags($this->data['html']), $this->object->sanitize($this->data['html']));
        $this->assertEquals(trim($this->data['text']), $this->object->sanitize($this->data['text']));
        $this->assertEquals('', $this->object->sanitize($this->data['code']));
        
        $param = array(Flag::ENCODE_TAGS => true);
        $this->assertEquals(htmlentities($this->data['code']), $this->object->sanitize($this->data['code'], $param));
                
        $params = array(Flag::MAXLENGHT => 5);
        $this->assertEquals('123456', $this->object->sanitize(' 123456 ', $params));
        $params[Flag::TRUNCATE] = true;
        $this->assertEquals('12345', $this->object->sanitize(' 123456 ', $params));
        $this->assertEquals('false', $this->object->sanitize(false));
        $this->assertEquals('true', $this->object->sanitize(true));
        $this->assertEquals('false', $this->object->sanitize('false'));
        $this->assertEquals('true', $this->object->sanitize('true'));
        $this->assertEquals('0', $this->object->sanitize('0'));
        $this->assertEquals('10', $this->object->sanitize(10));
    }
    
    public function testIsEmpty() {        
        $this->assertTrue($this->object->isEmpty(null));
        $this->assertTrue($this->object->isEmpty(''));
        $this->assertTrue($this->object->isEmpty(""));
        $this->assertTrue($this->object->isEmpty(" "));
        $this->assertTrue($this->object->isEmpty('        '));
        
        $this->assertFalse($this->object->isEmpty('0'));
        $this->assertFalse($this->object->isEmpty(0));
        $this->assertFalse($this->object->isEmpty('abc'));
        $this->assertFalse($this->object->isEmpty(true));
        $this->assertFalse($this->object->isEmpty('true'));
        $this->assertFalse($this->object->isEmpty('false'));
        $this->assertFalse($this->object->isEmpty(false));
    }
    
    public function testIsValid() {
        Memory::set('debug', true);
        foreach ($this->data as $k => $val) {
            $val2 = $this->object->sanitize($val);
            $this->assertTrue($this->object->isValid($val2), 'Error to validate the sanitized key: ' . $k);
        }
        $this->assertTrue($this->object->isValid(''));
        $this->assertFalse($this->object->isValid(' ')); //Não é vazio, então não é válido
        $this->assertTrue($this->object->isValid(true));
        $this->assertTrue($this->object->isValid(false));
        $this->assertTrue($this->object->isValid(null));
        $required = array(Flag::REQUIRED => true);
        $this->assertFalse($this->object->isValid('', $required));
        $this->assertFalse($this->object->isValid(' ', $required));
        $this->assertFalse($this->object->isValid(null, $required));
        $this->assertFalse($this->object->isValid(' ', $required));
        
        $this->assertTrue($this->object->isValid(true, $required));     //'true'
        $this->assertTrue($this->object->isValid(false, $required));    //'false'
        $this->assertTrue($this->object->isValid('abc', $required));
        $this->assertTrue($this->object->isValid(0, $required));
        $this->assertTrue($this->object->isValid('0', $required));
        
        $required[Flag::MINLENGHT] = 2;
        $this->assertFalse($this->object->isValid(' ', $required));
        $this->assertFalse($this->object->isValid('a', $required));
        $this->assertTrue($this->object->isValid('213412', $required));
        $required[Flag::MAXLENGHT] = 5;
        $this->assertFalse($this->object->isValid('213412', $required));
        $this->assertFalse($this->object->isValid('      ', $required));
        $this->assertTrue($this->object->isValid('12', $required));
        $this->assertTrue($this->object->isValid('12345', $required));
        
        //Minlenght without required
        $this->assertTrue($this->object->isValid('', array(Flag::MINLENGHT => 3)));
    }
}
