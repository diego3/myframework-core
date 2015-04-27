<?php
require_once 'util/datatype/DatatypeDate.php';

class DatatypeDateTest extends DatatypeBaseTest {

    /**
     * @var DatatypeDate
     */
    protected $object;
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        parent::setUp();
        $this->object = new DatatypeDate;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        
    }

    public function testValueOf() {
        $invalid = array('', '123412', '2341-213-1-12', '2oie/2/3212', '12-1av2-2012', 'a/b/c;a', '01/01/2001t');
        foreach ($invalid as $date) {
            $this->assertNull($this->object->valueOf($date));
        }
        
        $validDates = array('2001-10-23', '2014-01-01', '1999/11/3', '1990.3.9', '9999-12-31');
        $expectedDates = array('2001-10-23', '2014-01-01', '1999-11-3', '1990-3-9', '9999-12-31');
        foreach ($validDates as $i => $date) {
            $dt = $this->object->valueOf($date);
            $this->assertEquals($expectedDates[$i], $dt);
        }
    }
    
    public function testSanitize() {
        $invalid = array('', '123412', '2341-213-1-12', '22345/2/3212', '12-22-2012', 'a/b/c');
        foreach ($invalid as $date) {
            $this->assertNull($this->object->sanitize($date));
        }
        
        $validDates = array('2001-10-23', '2014-01-01', '1999/11/3', '1990.3.9', '9999-12-31');
        $expectedDates = array('2001-10-23', '2014-01-01', '1999-11-03', '1990-03-09', '9999-12-31');
        foreach ($validDates as $i => $date) {
            $dt = $this->object->sanitize($date);
            $this->assertInstanceOf('DateTime', $dt, $expectedDates[$i]);
            $this->assertEquals($expectedDates[$i], $dt->format('Y-m-d'));
        }
        
        $dt1 = $this->object->sanitize('21-01-2001', array(Flag::DATE_FORMAT_BRAZIL));
        $this->assertInstanceOf('DateTime', $dt1);
        $this->assertEquals('2001-01-21', $dt1->format('Y-m-d'));
        
        $dt2 = $this->object->sanitize('01-21-2001', array(Flag::DATE_FORMAT_USA));
        $this->assertInstanceOf('DateTime', $dt2);
        $this->assertEquals('2001-01-21', $dt2->format('Y-m-d'));
    }

    public function testIsValid() {
        $invalid = array('', '123412', '2341-213-1-12', '22345/2/3212', '12-22-2012', 'a/b/c', '10/10/2010');
        foreach ($invalid as $date) {
            $this->assertFalse($this->object->isValid($date));
        }
        
        $validDates = array('2001-10-23', '2014-01-01', '1999-11-3', '1990-3-9', '9999-12-31');
        foreach ($validDates as $i => $date) {
            $this->assertTrue($this->object->isValid($date, array(Flag::DATE_FORMAT_ISO)), $date);
        }
        
        $this->assertTrue($this->object->isValid('21-01-2001', array(Flag::DATE_FORMAT_BRAZIL)));
        $this->assertTrue($this->object->isValid('01-21-2001', array(Flag::DATE_FORMAT_USA)));
    }

    public function testHumanValue() {
    }
}
