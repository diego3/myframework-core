<?php

namespace MyFrameWork\Security;

use MyFrameWork\Security\Factory;
use MyFrameWork\Security\Cryptographer;

class CryptographerTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var Cryptographer
     */
    protected $cripto;

   
    protected function setUp() {
        $this->cripto = Factory::getCryptographer(Cryptographer::MD5);
    }

    
    protected function tearDown() {
        
    }

    public function testMd5VerifyPassword() {
        $hash = $this->cripto->encriptyPassword("admin");
        $expected = md5("admin" . ")*$&)#2");
        
        $this->assertEquals($expected , $hash);
        $this->assertTrue($this->cripto->verifyPassword("admin", $hash));
    }
    
    public function testBCryptVerifyPassword() {
        $bcrypt = Factory::getCryptographer(Cryptographer::BCRYPT);
        $hash_password = $bcrypt->encriptyPassword("admin");
        
        $hasOk = $bcrypt->verifyPassword("admin", $hash_password);
        $this->assertTrue($hasOk);
    }

}
