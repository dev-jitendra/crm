<?php

use LightnCandy\LightnCandy;
use LightnCandy\Runtime;
use LightnCandy\SafeString;
use PHPUnit\Framework\TestCase;

require_once(__DIR__ . '/test_util.php');

class LightnCandyTest extends TestCase
{
    
    public function testOn_compilePartial() {
        $method = new \ReflectionMethod('LightnCandy\LightnCandy', 'compilePartial');
        $this->assertEquals(false, $method->invokeArgs(null, array_by_ref(array(
            '{{"}}', array('flags' => LightnCandy::FLAG_HANDLEBARS)
        ))));
    }
    
    public function testOn_handleError() {
        $method = new \ReflectionMethod('LightnCandy\LightnCandy', 'handleError');
        $method->setAccessible(true);
        $this->assertEquals(false, $method->invokeArgs(null, array_by_ref(array(
            array('error' => array())
        ))));
        $this->assertEquals(true, $method->invokeArgs(null, array_by_ref(array(
            array('error' => array('some error'), 'flags' => array('errorlog' => 0, 'exception' => 0))
        ))));
    }
}
