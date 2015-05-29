<?php

require_once 'util/mycore.php';

class MycoreTest extends PHPUnit_Framework_TestCase {
    
    public function testStringFunctions() {
        $this->assertTrue(startsWith('abc', 'a'));
        $this->assertFalse(startsWith('abc', 'A'));
        $this->assertTrue(startsWith('abc', 'A', false));
        $this->assertTrue(endsWith('abc', 'c'));
        $this->assertFalse(endsWith('abc', 'C'));
        $this->assertTrue(endsWith('abc', 'C', false));
    }
    
    public function testGetValueFromArray() {
        $vetor = array('a' => 1, 'b' => '2', 'c' => true, 'd' => false);
        foreach ($vetor as $k => $v) {
            $this->assertEquals($v, getValueFromArray($vetor, $k));
            $this->assertEquals($v, getValueFromArray($vetor, $k, 'xyz'));
        }
        $this->assertNull(getValueFromArray($vetor, 'xyz'));
        $this->assertEquals('default value', getValueFromArray($vetor, 'xyz', 'default value'));
        $this->assertEquals(true, getValueFromArray($vetor, 'xyz', true));
        $this->assertEquals(false, getValueFromArray($vetor, 'xyz', false));
        $this->assertEquals('', getValueFromArray($vetor, 'xyz', ''));
    }
    
    public function testRemoveDuplicatedChar() {
        $this->assertEquals('abc', removeDuplicatedChar('abc'));
        $this->assertEquals('a b c', removeDuplicatedChar('a b c'));
        $this->assertEquals('a b c', removeDuplicatedChar('a  b  c'));
        $this->assertEquals('a b c', removeDuplicatedChar('a  b  c', ' '));
        $this->assertEquals('a b c', removeDuplicatedChar('a   b        c'));
        $this->assertEquals('axbxc', removeDuplicatedChar('axxbxxc', 'x'));
        $this->assertEquals('axbxc', removeDuplicatedChar('axxxxbxxxxc', 'x'));
        foreach (array('.', '?', '(', ')', '[', ']', '{', '}', '/', '\\', '*', '%', '$', '#', '@', '+', '-') as $char) {
            $text = 'a'.$char.$char.'bc' . str_repeat($char, 5);
            $this->assertEquals(
                'a'.$char.'bc'.$char, 
                removeDuplicatedChar($text, $char),
                'Original: [' . $text . ']'
            );
        }
    }
    
    public function testRemoveLineBreak() {
        $test = <<<'A'
1
                  2
        3
A;
        $this->assertEquals('123', removeLineBreak($test));
        $this->assertEquals('1 2 3', removeLineBreak($test, ' '));
        $this->assertEquals('1-2-3-', removeLineBreak($test, '-'));
$test.= <<<'A'
    
   

    4-5
A;
        $this->assertEquals('1234-5', removeLineBreak($test));
        $this->assertEquals('1 2 3 4-5', removeLineBreak($test, ' '));
        $this->assertEquals('1*2*3*4-5*', removeLineBreak($test, '*'));
        $this->assertEquals('1*2*3*4-5*', removeLineBreak(trim($test), '*'));
    }
    
    public function testTruncateDecimal() {
        $this->assertEquals(1.2, truncateDecimal(1.2341, 1));
        $this->assertEquals(1.2, truncateDecimal(1.2741, 1));
        $this->assertEquals(1.3, round(1.2741, 1));
        $this->assertEquals(1.274, truncateDecimal(1.2741, 3));
        $this->assertEquals(1.2741, truncateDecimal(1.2741, 4));
        $this->assertEquals(1.2741, truncateDecimal(1.2741, 5));
        $this->assertEquals(1.0, truncateDecimal(1.1, 0));
        $this->assertEquals(1.0, truncateDecimal(1.121, 0));
        $this->assertEquals(1.121, truncateDecimal(1.121, -1));
        $this->assertEquals(1.1, truncateDecimal(1.1, 2));
        $this->assertEquals(1.1, truncateDecimal(1.1, 7));
        
    }
    /*
    public function testIsValidEmail() {
        $validEmails = array(
            'first.last@iana.org',
            '1234567890123456789012345678901234567890123456789012345678901234@iana.org',
            'first.last@[12.34.56.78]',
            '"Fred\ Bloggs"@iana.org',
            'Ima.Fool@iana.org',
            'teset@tenhocertezawqreqnadaver.xom.it',
            'xyz@gmail.com2231324123421341234123234121321'
        );
        foreach ($validEmails as $mail) {
            $this->assertTrue(isValidEmail($mail), $mail);
        }
        $invalidEmails = array(
            'xyz@gmailcom2231324123421341234123234121321',
            '"Ima Fool"@iana.org',
            'first.last@example.123',
            'first.last@com',
            'a(a(b(c)d(e(f))g)h(i)j)@iana.org'
        );
        foreach ($invalidEmails as $mail) {
            $this->assertFalse(isValidEmail($mail), $mail);
        }
        
        $validEmails = array('xyz@gmail.com', 'test@uol.com.br', 'home.teset@terra.com.br', 'Ima.Fool@iana.org');
        foreach ($validEmails as $mail) {
            $this->assertTrue(isValidEmail($mail, true), $mail);
        }
        
        $invalidEmails = array(
            'teset@tenhocertezawqreqnadaver.xom.it',
            'xyz@gmail.com2231324123421341234123234121321',
            'first.last@[12.34.56.78]',
            'teset@terreiro.terra.com'
        );
        foreach ($invalidEmails as $mail) {
            $this->assertFalse(isValidEmail($mail, true), $mail);
        }
    }
    */
    public function testIsAssoc() {
        $this->assertFalse(is_assoc(array()));
        $this->assertFalse(is_assoc(2));
        $this->assertFalse(is_assoc('2'));
        $this->assertFalse(is_assoc(null));
        
        $this->assertFalse(is_assoc(array(1, 2, 4)));
        $this->assertFalse(is_assoc(array('a' => 1, 2, 'b' => 4)));
        
        $this->assertTrue(is_assoc(array('a' => 2)));
        $this->assertTrue(is_assoc(array('a' => 'xyz', 'b' => array())));
    }
    
    public function testHashIt() {
        $this->assertEquals(md5('teste' . UPSALT), hashit('teste'));
        $this->assertEquals(md5('teste' . '12345'), hashit('teste', '12345'));
        $this->assertEquals(md5('teste' . '12345'), hashit('teste12345', ''));
    }
}