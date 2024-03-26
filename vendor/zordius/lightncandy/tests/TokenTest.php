<?php

use LightnCandy\LightnCandy;
use LightnCandy\Runtime;
use LightnCandy\SafeString;
use PHPUnit\Framework\TestCase;

require_once(__DIR__ . '/test_util.php');

class TokenTest extends TestCase
{
    
    public function testOn_toString() {
        $method = new \ReflectionMethod('LightnCandy\Token', 'toString');
        $this->assertEquals('c', $method->invokeArgs(null, array_by_ref(array(
            array(0, 'a', 'b', 'c', 'd', 'e')
        ))));
        $this->assertEquals('cd', $method->invokeArgs(null, array_by_ref(array(
            array(0, 'a', 'b', 'c', 'd', 'e', 'f')
        ))));
        $this->assertEquals('qd', $method->invokeArgs(null, array_by_ref(array(
            array(0, 'a', 'b', 'c', 'd', 'e', 'f'), array(3 => 'q')
        ))));
    }
}
