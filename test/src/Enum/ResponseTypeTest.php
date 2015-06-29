<?php

namespace MyFrameWork\Enum;

use MyFrameWork\Enum\ResponseType;
use MyFrameWork\Factory;

/**
 * Description of ResponseTypeTest
 *
 * @author Diego Rosa dos Santos <diegosantos@alphaeditora.com.br>
 */
class ResponseTypeTest extends \PHPUnit_Framework_TestCase {
    
    protected function setUp() {
        
    }
    
    public function testIsValidWithEmptParameter() {
        //valid uses
        $this->assertTrue(ResponseType::isValid(ResponseType::EMPT));
        $this->assertTrue(ResponseType::isValid("EMPTY"));
        
        //invalid uses
        $this->assertFalse(ResponseType::isValid("Empty"));
        $this->assertFalse(ResponseType::isValid("empty"));
        $this->assertFalse(ResponseType::isValid("empt"));
        $this->assertFalse(ResponseType::isValid("Empt"));
    }
    
    public function testOnFactoryResponseTypes() {
        $htmlInstace = Factory::response(ResponseType::HTML);
        $this->assertInstanceOf("MyFrameWork\\Response\\HtmlResponse", $htmlInstace);
        
        $emtyInstace = Factory::response(ResponseType::EMPT);
        $this->assertInstanceOf("MyFrameWork\\Response\\EmptyResponse", $emtyInstace);
    }
    
    public function testIsValidWhenTakeALotOfArguments() {
        //all types that should be pass
        $this->assertTrue(ResponseType::isValid(ResponseType::CSV));
        $this->assertTrue(ResponseType::isValid(ResponseType::HTML));
        $this->assertTrue(ResponseType::isValid(ResponseType::JSON));
        $this->assertTrue(ResponseType::isValid(ResponseType::XML));
        $this->assertTrue(ResponseType::isValid(ResponseType::XLS));
        $this->assertTrue(ResponseType::isValid(ResponseType::EMPT));
    }
}
