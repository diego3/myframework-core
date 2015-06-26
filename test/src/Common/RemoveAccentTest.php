<?php

namespace MyFrameWork\Common;

use MyFrameWork\Common\RemoveAccent;

class RemoveAccentTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var RemoveAccent
     */
    protected $remover;

    
    protected function setUp() {
        $this->remover = new RemoveAccent;
    }

    
    protected function tearDown() {
        
    }

    public function testFilter() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

}
