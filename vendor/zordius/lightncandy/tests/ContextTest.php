<?php

use LightnCandy\LightnCandy;
use LightnCandy\Runtime;
use LightnCandy\SafeString;
use PHPUnit\Framework\TestCase;

require_once(__DIR__ . '/test_util.php');

class ContextTest extends TestCase
{
    
    public function testOn_updateHelperTable() {
        $method = new \ReflectionMethod('LightnCandy\Context', 'updateHelperTable');
        $method->setAccessible(true);
        $this->assertEquals(array(), $method->invokeArgs(null, array_by_ref(array(
            array(), array()
        ))));
        $this->assertEquals(array('flags' => array('exhlp' => 1), 'helpers' => array('abc' => 1)), $method->invokeArgs(null, array_by_ref(array(
            array('flags' => array('exhlp' => 1)), array('helpers' => array('abc'))
        ))));
        $this->assertEquals(array('error' => array('You provide a custom helper named as \'abc\' in options[\'helpers\'], but the function abc() is not defined!'), 'flags' => array('exhlp' => 0)), $method->invokeArgs(null, array_by_ref(array(
            array('error' => array(), 'flags' => array('exhlp' => 0)), array('helpers' => array('abc'))
        ))));
        $this->assertEquals(array('flags' => array('exhlp' => 1), 'helpers' => array('\\LightnCandy\\Runtime::raw' => '\\LightnCandy\\Runtime::raw')), $method->invokeArgs(null, array_by_ref(array(
            array('flags' => array('exhlp' => 1), 'helpers' => array()), array('helpers' => array('\\LightnCandy\\Runtime::raw'))
        ))));
        $this->assertEquals(array('flags' => array('exhlp' => 1), 'helpers' => array('test' => '\\LightnCandy\\Runtime::raw')), $method->invokeArgs(null, array_by_ref(array(
            array('flags' => array('exhlp' => 1), 'helpers' => array()), array('helpers' => array('test' => '\\LightnCandy\\Runtime::raw'))
        ))));
    }
}
