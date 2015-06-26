<?php

/**
 * 
 *
 * @author Diego Rosa dos Santos <diegosantos@alphaeditora.com.br>
 */
class substringTest extends PHPUnit_Framework_TestCase {
    
    protected $instance;
    
    protected function setUp() {
        
    }
    
    protected function tearDown() {
        
    }
    
    public function testGetPageNumberInTheMostSimpleCase() {
        $key = "image_upload_imgnum0_pagenum3";
        $this->assertEquals("0", $this->getPageNumber($key));
    }
    
    public function testGetPageNumberWhenPageNumberIsTwoDigits() {
        $key = "image_upload_imgnum10_pagenum10";
        $this->assertEquals(10, $this->getPageNumber($key));
    }
    
    public function testGetPageNumberInTheRealCase() {
        for($i = 1; $i <= 20; $i++) {
            $key = "image_upload_imgnum{$i}_pagenum{$i}";
            $this->assertEquals($i, $this->getPageNumber($key));
        }
    }
    
    private function getPageNumber($full_string) {
        $part1 = substr($full_string, strpos($full_string, 'imgnum'));
        $part2 = substr($part1, 0, strpos($part1, "_"));
        return str_replace("imgnum", "", $part2);
    }
    
    public function testGetPageNumberAlgorithm() {
        $key   = "image_upload_imgnum0_pagenum3";
        $part1 = substr($key, strpos($key, 'imgnum'));
        
        $this->assertEquals("imgnum0_pagenum3", $part1);
        
        $part2 = substr($part1, 0, strpos($part1, "_"));
        
        $this->assertEquals("imgnum0", $part2);
        
        $page_number = str_replace("imgnum", "", $part2);
        
        $this->assertEquals("0", $page_number);
    }
    
}
