<?php

use LightnCandy\LightnCandy;
use LightnCandy\Runtime;
use LightnCandy\SafeString;
use PHPUnit\Framework\TestCase;

require_once(__DIR__ . '/test_util.php');

class SafeStringTest extends TestCase
{
    
    public function testOn_stripExtendedComments() {
        $method = new \ReflectionMethod('LightnCandy\SafeString', 'stripExtendedComments');
        $this->assertEquals('abc', $method->invokeArgs(null, array_by_ref(array(
            'abc'
        ))));
        $this->assertEquals('abc{{!}}cde', $method->invokeArgs(null, array_by_ref(array(
            'abc{{!}}cde'
        ))));
        $this->assertEquals('abc{{! }}cde', $method->invokeArgs(null, array_by_ref(array(
            'abc{{!----}}cde'
        ))));
    }
    
    public function testOn_escapeTemplate() {
        $method = new \ReflectionMethod('LightnCandy\SafeString', 'escapeTemplate');
        $this->assertEquals('abc', $method->invokeArgs(null, array_by_ref(array(
            'abc'
        ))));
        $this->assertEquals('a\\\\bc', $method->invokeArgs(null, array_by_ref(array(
            'a\bc'
        ))));
        $this->assertEquals('a\\\'bc', $method->invokeArgs(null, array_by_ref(array(
            'a\'bc'
        ))));
    }
}
