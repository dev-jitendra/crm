<?php

use LightnCandy\LightnCandy;
use LightnCandy\Runtime;
use LightnCandy\SafeString;
use PHPUnit\Framework\TestCase;

require_once(__DIR__ . '/test_util.php');

class EncoderTest extends TestCase
{
    
    public function testOn_raw() {
        $method = new \ReflectionMethod('LightnCandy\Encoder', 'raw');
        $this->assertEquals(true, $method->invokeArgs(null, array_by_ref(array(
            array('flags' => array('jstrue' => 0, 'mustlam' => 0, 'lambda' => 0)), true
        ))));
        $this->assertEquals('true', $method->invokeArgs(null, array_by_ref(array(
            array('flags' => array('jstrue' => 1)), true
        ))));
        $this->assertEquals('', $method->invokeArgs(null, array_by_ref(array(
            array('flags' => array('jstrue' => 0, 'mustlam' => 0, 'lambda' => 0)), false
        ))));
        $this->assertEquals('false', $method->invokeArgs(null, array_by_ref(array(
            array('flags' => array('jstrue' => 1)), false
        ))));
        $this->assertEquals(false, $method->invokeArgs(null, array_by_ref(array(
            array('flags' => array('jstrue' => 1)), false, true
        ))));
        $this->assertEquals('Array', $method->invokeArgs(null, array_by_ref(array(
            array('flags' => array('jstrue' => 1, 'jsobj' => 0)), array('a', 'b')
        ))));
        $this->assertEquals('a,b', $method->invokeArgs(null, array_by_ref(array(
            array('flags' => array('jstrue' => 1, 'jsobj' => 1, 'mustlam' => 0, 'lambda' => 0)), array('a', 'b')
        ))));
        $this->assertEquals('[object Object]', $method->invokeArgs(null, array_by_ref(array(
            array('flags' => array('jstrue' => 1, 'jsobj' => 1)), array('a', 'c' => 'b')
        ))));
        $this->assertEquals('[object Object]', $method->invokeArgs(null, array_by_ref(array(
            array('flags' => array('jstrue' => 1, 'jsobj' => 1)), array('c' => 'b')
        ))));
        $this->assertEquals('a,true', $method->invokeArgs(null, array_by_ref(array(
            array('flags' => array('jstrue' => 1, 'jsobj' => 1, 'mustlam' => 0, 'lambda' => 0)), array('a', true)
        ))));
        $this->assertEquals('a,1', $method->invokeArgs(null, array_by_ref(array(
            array('flags' => array('jstrue' => 0, 'jsobj' => 1, 'mustlam' => 0, 'lambda' => 0)), array('a',true)
        ))));
        $this->assertEquals('a,', $method->invokeArgs(null, array_by_ref(array(
            array('flags' => array('jstrue' => 0, 'jsobj' => 1, 'mustlam' => 0, 'lambda' => 0)), array('a',false)
        ))));
        $this->assertEquals('a,false', $method->invokeArgs(null, array_by_ref(array(
            array('flags' => array('jstrue' => 1, 'jsobj' => 1, 'mustlam' => 0, 'lambda' => 0)), array('a',false)
        ))));
    }
    
    public function testOn_enc() {
        $method = new \ReflectionMethod('LightnCandy\Encoder', 'enc');
        $this->assertEquals('a', $method->invokeArgs(null, array_by_ref(array(
            array('flags' => array('mustlam' => 0, 'lambda' => 0)), 'a'
        ))));
        $this->assertEquals('a&amp;b', $method->invokeArgs(null, array_by_ref(array(
            array('flags' => array('mustlam' => 0, 'lambda' => 0)), 'a&b'
        ))));
        $this->assertEquals('a&#039;b', $method->invokeArgs(null, array_by_ref(array(
            array('flags' => array('mustlam' => 0, 'lambda' => 0)), 'a\'b'
        ))));
    }
    
    public function testOn_encq() {
        $method = new \ReflectionMethod('LightnCandy\Encoder', 'encq');
        $this->assertEquals('a', $method->invokeArgs(null, array_by_ref(array(
            array('flags' => array('mustlam' => 0, 'lambda' => 0)), 'a'
        ))));
        $this->assertEquals('a&amp;b', $method->invokeArgs(null, array_by_ref(array(
            array('flags' => array('mustlam' => 0, 'lambda' => 0)), 'a&b'
        ))));
        $this->assertEquals('a&#x27;b', $method->invokeArgs(null, array_by_ref(array(
            array('flags' => array('mustlam' => 0, 'lambda' => 0)), 'a\'b'
        ))));
        $this->assertEquals('&#x60;a&#x27;b', $method->invokeArgs(null, array_by_ref(array(
            array('flags' => array('mustlam' => 0, 'lambda' => 0)), '`a\'b'
        ))));
    }
}
