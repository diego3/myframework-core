<?php
namespace MyFrameWork\DataType;

use MyFrameWork\DataType\Datatype;
use MyFrameWork\Enum\Flag;

class MyDatatype extends Datatype {
    /**
     * Para efeitos de teste se o $value == $params['value'] então o valor é válido
     * @return boolean
     */
    protected function _isValid($value, $params) {
        return ($value == getValueFromArray($params, 'value')) && parent::_isValid($value, $params);
    }

    /**
     * Para efeitos de sanitização se $params['value'] não for vazio, concatena-o ao final do conteúdo
     * @return string
     */
    protected function _sanitize($value, $params) {
        $value = parent::_sanitize($value, $params);
        $extra = getValueFromArray($params, 'value', '');
        if (empty($extra)) {
            return $value;
        }
        return $value . $extra;
    }
    
    public function setDefault($key, $value) {
        $this->defaultParams[$key] = $value;
    }
    
    /**
     * Método de sanitização chamado dinamicamente
     */
    public function tiraespaco($value) {
        return str_replace(' ', '', $value);
    }
    
    /**
     * Método de validação chamado dinamicamente
     */
    public function validTiraespaco($value) {
        return strpos($value, ' ') === false;
    }
}
/**
 * Generated by PHPUnit_SkeletonGenerator on 2014-07-16 at 13:54:19.
 */
class DatatypeTest extends \DatatypeBaseTest {

    /**
     * @var MyDatatype
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        parent::setUp();
        $this->object = new MyDatatype();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        
    }

    public function testSanitize() {
        foreach ($this->data as $k => $val) {
            $this->assertEquals($val, $this->object->sanitize($val), 'Error to sanitize the key ' . $k);
        }
        $this->assertEquals($val . 'test', $this->object->sanitize($val, array('value' => 'test')));
        
        //Test empty value
        $this->assertEmpty($this->object->sanitize(''));
        $this->assertEmpty($this->object->sanitize(null));
        $this->assertNotNull($this->object->sanitize(''));
        $this->assertNull($this->object->sanitize(null));
        
        //Test default value
        $params = array(Flag::DEFAULT_VALUE => 'default test');
        $this->assertEquals('', $this->object->sanitize('', $params));
        $this->assertEquals('default test', $this->object->sanitize(null, $params));
        $this->assertEquals('0', $this->object->sanitize('0', $params));
        $this->assertEquals('   ', $this->object->sanitize('   ', $params));
        
        $this->object->setDefault(Flag::DEFAULT_VALUE, 'teste');
        $this->assertEquals('teste', $this->object->sanitize(null, $params));
        
        //Test boolean value
        $this->assertEquals('teste', $this->object->sanitize('t e s t e', array('tiraespaco' => true)));
        $this->assertEquals('t e s t e', $this->object->sanitize('t e s t e', array('tiraespaco' => false)));
        
        //Formato limpo, usando valores lógicos diretamente como valor do parâmetro
        $this->assertEquals('teste', $this->object->sanitize('t e s t e', array('tiraespaco')));
        
        //Mais de um valor do parâmetro sem função associada
        $this->assertEquals('teste', $this->object->sanitize('t e s t e', array('tiraespaco', 'faznada')));
    }

    public function testIsValid() {
        foreach ($this->data as $k => $val) {
            $this->assertFalse($this->object->isValid($val), 'Error to valid the key ' . $k);
            $this->assertTrue($this->object->isValid($val, array('value' => $val)), 'Error to valid the key ' . $k);
        }
        $this->assertTrue($this->object->isValid(null));
        $this->assertTrue($this->object->isValid(''));
        $this->assertFalse($this->object->isValid('a'));
        
        //Required test
        $this->assertTrue($this->object->isValid(null, array(Flag::REQUIRED => false)));
        $this->assertFalse($this->object->isValid(null, array(Flag::REQUIRED => true, 'value' => 'a')));
        
        //Default value test
        $params = array(Flag::REQUIRED => true, Flag::DEFAULT_VALUE => 'a', 'value' => 'a');
        $this->assertFalse($this->object->isValid(null, $params));
        $this->assertEquals('aa', $this->object->sanitize(null, $params));
        $params2 = $params;
        $params2['value'] .= 'a';
        $this->assertTrue($this->object->isValid($this->object->sanitize(null, $params), $params2));
        
        //Dinamic metohd
        $this->assertTrue($this->object->isValid('a b c', array('value' => 'a b c')));
        $this->assertFalse($this->object->isValid('a b c', array('value' => 'a b c', 'tiraespaco' => true)));
        $this->assertFalse($this->object->isValid('a b c', array('value' => 'a b c', 'tiraespaco')));
        $this->assertTrue($this->object->isValid('abc', array('value' => 'abc', 'tiraespaco')));
    }

    public function testToHumanFormat() {
        $this->assertEquals('a', $this->object->toHumanFormat('a', array()));
        $params = array(Flag::REQUIRED => true, Flag::DEFAULT_VALUE => 'a', 'value' => 'a');
        $this->assertEquals('a', $this->object->toHumanFormat('a', $params));
        $params[Flag::MASK] = '"%s"';
        $this->assertEquals('"a"', $this->object->toHumanFormat('a', $params));
        $params[Flag::MASK] = 'R$ %2.3f';
        $this->assertEquals('R$ 2.200', $this->object->toHumanFormat('2.2', $params));
    }

    public function testInvalidParams() {
        $this->markTestIncomplete();
        //TODO passar um array com indice numérico
    }
}
