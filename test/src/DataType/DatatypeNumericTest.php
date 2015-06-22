<?php
require_once 'util/datatype/DatatypeNumeric.php';

/**
 * Generated by PHPUnit_SkeletonGenerator on 2014-07-18 at 17:43:39.
 */
class DatatypeNumericTest extends DatatypeBaseTest  {

    /**
     * @var DatatypeNumeric
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->object = new DatatypeNumeric;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        
    }
    
    public function testValueOf() {
        $this->assertEquals(null, $this->object->valueOf(''));
        $this->assertEquals(0, $this->object->valueOf('0'));
        $this->assertEquals(null, $this->object->valueOf('    '));
        $this->assertEquals(null, $this->object->valueOf('abcde'));
        $this->assertEquals(23412, $this->object->valueOf('abcde23412'));
        $this->assertEquals(345234.12, $this->object->valueOf('345afrço234.12'));
        $this->assertEquals(-32, $this->object->valueOf('-3aaa2'));
        $this->assertEquals(32, $this->object->valueOf('3aaa2'));
        $this->assertEquals(32.23, $this->object->valueOf('3aaa2,23'));
        $this->assertEquals(null, $this->object->valueOf('3.2.2.3'));
        $this->assertEquals(null, $this->object->valueOf('3,2.3'));
        $this->assertEquals(0, $this->object->valueOf('0.0'));
        $this->assertEquals(0, $this->object->valueOf(floatval('0.000')));
        $this->assertEquals(0, $this->object->valueOf(0));
        $this->assertEquals(234213, $this->object->valueOf('234213'));
        $this->assertEquals(2342.13, $this->object->valueOf('2342.13'));
        $this->assertEquals(2342.13, $this->object->valueOf('2342,13'));
        $this->assertEquals(0.2, $this->object->valueOf(0.2));
        $this->assertEquals(312.2, $this->object->valueOf(312.2));
        $this->assertEquals(-312.2, $this->object->valueOf(-312.2));
        $this->assertEquals(312, $this->object->valueOf(312));
        $this->assertEquals(-312, $this->object->valueOf(-312));
    }
    
    public function testIsEmpty() {
        $this->assertTrue($this->object->isEmpty(''));
        $this->assertTrue($this->object->isEmpty('    '));
        $this->assertFalse($this->object->isEmpty('abcde'));
        $this->assertFalse($this->object->isEmpty('12.0.0'));
        $this->assertTrue($this->object->isEmpty('0'));
        $this->assertTrue($this->object->isEmpty('0.0'));
        $this->assertTrue($this->object->isEmpty(floatval('0.000')));
        $this->assertTrue($this->object->isEmpty(0));
        
        $this->assertFalse($this->object->isEmpty('abcde23412'));
        $this->assertFalse($this->object->isEmpty('234213'));
        $this->assertFalse($this->object->isEmpty('2342.13'));
        $this->assertFalse($this->object->isEmpty('2342,13'));
        $this->assertFalse($this->object->isEmpty('aaa12.00'));
        $this->assertFalse($this->object->isEmpty(0.2));
        $this->assertFalse($this->object->isEmpty(312.2));
        $this->assertFalse($this->object->isEmpty(-312.2));
        $this->assertFalse($this->object->isEmpty(312));
        $this->assertFalse($this->object->isEmpty(-312));
    }

    public function testDecimalsize() {
        $params = array(Flag::DECIMAL_SIZE => 2);
        $this->assertEquals(2.89, $this->object->sanitize('2.8932', $params));
        $this->assertEquals(2.89, $this->object->sanitize(2.8932, $params));
        $this->assertEquals(2.1, $this->object->sanitize(2.1, $params));
        $this->assertEquals(2.1, $this->object->sanitize('2.10000', $params));
        $this->assertTrue($this->object->validDecimalsize(2.89, $params));
        $this->assertTrue($this->object->validDecimalsize('2.89', $params));
        $this->assertTrue($this->object->validDecimalsize(2.1, $params));
        $this->assertTrue($this->object->validDecimalsize(32, $params));
        $this->assertFalse($this->object->validDecimalsize('2.8942', $params));
        $this->assertFalse($this->object->validDecimalsize(2.8942, $params));
        $this->assertFalse($this->object->validDecimalsize('asdfas', $params));
        
        $params[Flag::TRUNCATE] = true;
        $this->assertEquals(2.89, $this->object->sanitize(2.8932, $params));
        $this->assertTrue($this->object->isValid(2.89, $params));
        $this->assertTrue($this->object->isValid('2.89', $params));
        $this->assertTrue($this->object->isValid(2.1, $params));
        $this->assertTrue($this->object->isValid(32, $params));
        $this->assertFalse($this->object->isValid('2.8942', $params));
        $this->assertFalse($this->object->isValid(2.8942, $params));
        $this->assertFalse($this->object->validDecimalsize('', $params));
        
        $params[Flag::TRUNCATE] = false;
        $params[Flag::DECIMAL_SIZE] = 1;
        $this->assertEquals(2.9, $this->object->sanitize('2,8932', $params));
        $this->assertEquals(2.9, $this->object->sanitize(2.8932, $params));
        $this->assertNull($this->object->sanitize('asdfas', $params));
        
        $params[Flag::TRUNCATE] = true;
        $this->assertEquals(2.8, $this->object->sanitize(2.8932, $params));
    }
    
    public function testRequiredDefault() {
        $this->assertTrue($this->object->isValid('0'));
        $this->assertTrue($this->object->isValid(1234));
        $this->assertTrue($this->object->isValid(123.4));
        $this->assertFalse($this->object->isValid('asdfa'));
        
        $params = array(Flag::REQUIRED);
        $this->assertEquals(1234, $this->object->sanitize(1234, $params));
        $this->assertEquals(123.4, $this->object->sanitize(123.4, $params));
        $this->assertEquals(0, $this->object->sanitize('0', $params));
        $this->assertNull($this->object->sanitize('asdfa', $params));
        $this->assertTrue($this->object->isValid(1234, $params));
        $this->assertTrue($this->object->isValid(123.4, $params));
        $this->assertFalse($this->object->isValid('0', $params));
        $this->assertFalse($this->object->isValid('asdfa', $params));
        
        $params[Flag::DEFAULT_VALUE] = 23;
        //Não afeta o método isValid, mas afeta o método sanitize
        $this->assertEquals(1234, $this->object->sanitize(1234, $params));
        $this->assertEquals(123.4, $this->object->sanitize(123.4, $params));
        $this->assertEquals(23, $this->object->sanitize('0', $params));
        $this->assertEquals(23, $this->object->sanitize('asdfa', $params));
        $this->assertTrue($this->object->isValid(1234, $params));
        $this->assertTrue($this->object->isValid(123.4, $params));
        $this->assertFalse($this->object->isValid('0', $params));
        $this->assertFalse($this->object->isValid('asdfa', $params));
    }

    public function testMinMax() {
        $param1 = array(Flag::MIN_VALUE_INCLUSIVE => 5);
        $param2 = array(Flag::MAX_VALUE_INCLUSIVE => 5);
        $this->assertTrue($this->object->isValid(5, $param1));
        $this->assertTrue($this->object->isValid(6, $param1));
        $this->assertTrue($this->object->isValid(10, $param1));
        $this->assertFalse($this->object->isValid(4.99, $param1));
        $this->assertFalse($this->object->isValid(-6, $param1));
        $this->assertFalse($this->object->isValid(0, $param1));
        
        $this->assertTrue($this->object->isValid(5, $param2));
        $this->assertTrue($this->object->isValid(4.99, $param2));
        $this->assertTrue($this->object->isValid(-6, $param2));
        $this->assertTrue($this->object->isValid(0, $param2));
        $this->assertFalse($this->object->isValid(6, $param2));
        $this->assertFalse($this->object->isValid(10, $param2));
        
        $param3 = array(Flag::MIN_VALUE_INCLUSIVE => 5, Flag::MAX_VALUE_INCLUSIVE => 6);
        $this->assertFalse($this->object->isValid('', $param3));
        $this->assertTrue($this->object->isValid(5, $param3));
        $this->assertTrue($this->object->isValid(6, $param3));
        $this->assertFalse($this->object->isValid(4, $param3));
        $this->assertFalse($this->object->isValid(7, $param3));
        
        $param4 = array(Flag::MIN_VALUE_INCLUSIVE => 6, Flag::MAX_VALUE_INCLUSIVE => 5);
        $this->assertFalse($this->object->isValid(5, $param4));
        $this->assertFalse($this->object->isValid(6, $param4));
        $this->assertFalse($this->object->isValid(4, $param4));
        $this->assertFalse($this->object->isValid(7, $param4));
    }
    
    public function testPositiveNegative() {
        $this->assertEquals('', $this->object->positive('')); //invalid value, return it
        $this->assertEquals(21, $this->object->positive(-21));
        $this->assertEquals(0, $this->object->positive(0));
        $this->assertEquals(22, $this->object->positive(22));
        $this->assertEquals('', $this->object->negative('')); //invalid value, return it
        $this->assertEquals(-21, $this->object->negative(-21));
        $this->assertEquals(0, $this->object->negative(0));
        $this->assertEquals(-22, $this->object->negative(22));
        
        $param1 = array(Flag::POSITIVE_NUMBER);
        $param2 = array(Flag::NEGATIVE_NUMBER);
        $this->assertTrue($this->object->isValid(5, $param1));
        $this->assertTrue($this->object->isValid(6.32, $param1));
        $this->assertTrue($this->object->isValid(10, $param1));
        $this->assertFalse($this->object->isValid(-123, $param1));
        $this->assertFalse($this->object->isValid(-6, $param1));
        $this->assertFalse($this->object->isValid(0, $param1));
        
        $this->assertTrue($this->object->isValid(-5, $param2));
        $this->assertTrue($this->object->isValid(-4.99, $param2));
        $this->assertTrue($this->object->isValid(-6, $param2));
        $this->assertFalse($this->object->isValid(0, $param2));
        $this->assertFalse($this->object->isValid(6, $param2));
        $this->assertFalse($this->object->isValid(10, $param2));
        
        $param3 = array(Flag::POSITIVE_NUMBER, Flag::NEGATIVE_NUMBER);
        $this->assertFalse($this->object->isValid(5, $param3));
        $this->assertFalse($this->object->isValid(-6, $param3));
        $this->assertFalse($this->object->isValid(0, $param3));
        $this->assertFalse($this->object->isValid(1, $param3));
    }
}