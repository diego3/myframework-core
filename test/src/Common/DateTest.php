<?php

namespace MyFrameWork\Common;

use MyFrameWork\Common\Date;

/**
 * 
 *
 * @author Diego Rosa dos Santos<diegosantos@alphaeditora.com.br>
 */
class DateTest extends \PHPUnit_Framework_TestCase {
    /**
     *
     * @var Date 
     */
    protected $dt;
    
    protected function setUp() {
        $this->dt = new Date;
    }
    
    protected function tearDown() {}
    
    /**
     * @expectedException Exception
     */
    public function testOPrazoEstaVencidoDataInvalidaDeleLancarException() {
        $prazo = "2015-30-99";
        $this->dt->isValid($prazo);
    }
    
    public function testCasoEmQuePrazoEstaVencido() {
        $dt = new \Datetime();
        $prazo = $dt->sub(\DateInterval::createFromDateString("4 months"));//"2015-05-30";
        
        $this->assertInstanceOf("DateTime", $prazo);
        $this->assertTrue(is_object($prazo));
        $this->assertTrue(is_string($prazo->format("Y-m-d")));
        $this->assertTrue($this->dt->isValid($prazo->format("Y-m-d")));
        
        $prazo2 = $prazo->format("y-m-d h:i:s");
        $this->assertTrue($this->dt->isValid($prazo2));
    }
    
    public function testCasoEmQuePrazoNaoEstaVencido() {
        $dt = new \Datetime();
        $prazo = $dt->add(\DateInterval::createFromDateString("3 months + 15 days"));
        
        $this->assertFalse($this->dt->isValid($prazo->format("Y-m-d")));
    }
    
}
