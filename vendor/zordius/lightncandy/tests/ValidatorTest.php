<?php

use LightnCandy\LightnCandy;
use LightnCandy\Runtime;
use LightnCandy\SafeString;
use PHPUnit\Framework\TestCase;

require_once(__DIR__ . '/test_util.php');

class ValidatorTest extends TestCase
{
    
    public function testOn_delimiter() {
        $method = new \ReflectionMethod('LightnCandy\Validator', 'delimiter');
        $method->setAccessible(true);
        $this->assertEquals(null, $method->invokeArgs(null, array_by_ref(array(
            array_fill(0, 11, ''), array()
        ))));
        $this->assertEquals(null, $method->invokeArgs(null, array_by_ref(array(
            array(0, 0, 0, 0, 0, '{{', '#', '...', '}}'), array()
        ))));
        $this->assertEquals(true, $method->invokeArgs(null, array_by_ref(array(
            array(0, 0, 0, 0, 0, '{', '#', '...', '}'), array()
        ))));
    }
    
    public function testOn_operator() {
        $method = new \ReflectionMethod('LightnCandy\Validator', 'operator');
        $method->setAccessible(true);
        $this->assertEquals(null, $method->invokeArgs(null, array_by_ref(array(
            '', array(), array()
        ))));
        $this->assertEquals(2, $method->invokeArgs(null, array_by_ref(array(
            '^', array('usedFeature' => array('isec' => 1), 'level' => 0, 'currentToken' => array(0,0,0,0,0,0,0,0), 'elselvl' => array(), 'flags' => array('spvar' => 0), 'elsechain' => false, 'helperresolver' => 0), array(array('foo'))
        ))));
        $this->assertEquals(true, $method->invokeArgs(null, array_by_ref(array(
            '/', array('stack' => array('[with]', '#'), 'level' => 1, 'currentToken' => array(0,0,0,0,0,0,0,'with'), 'flags' => array('nohbh' => 0)), array(array())
        ))));
        $this->assertEquals(4, $method->invokeArgs(null, array_by_ref(array(
            '#', array('usedFeature' => array('sec' => 3), 'level' => 0, 'currentToken' => array(0,0,0,0,0,0,0,0), 'flags' => array('spvar' => 0), 'elsechain' => false, 'elselvl' => array(), 'helperresolver' => 0), array(array('x'))
        ))));
        $this->assertEquals(5, $method->invokeArgs(null, array_by_ref(array(
            '#', array('usedFeature' => array('if' => 4), 'level' => 0, 'currentToken' => array(0,0,0,0,0,0,0,0), 'flags' => array('spvar' => 0, 'nohbh' => 0), 'elsechain' => false, 'elselvl' => array(), 'helperresolver' => 0), array(array('if'))
        ))));
        $this->assertEquals(6, $method->invokeArgs(null, array_by_ref(array(
            '#', array('usedFeature' => array('with' => 5), 'level' => 0, 'flags' => array('nohbh' => 0, 'runpart' => 0, 'spvar' => 0), 'currentToken' => array(0,0,0,0,0,0,0,0), 'elsechain' => false, 'elselvl' => array(), 'helperresolver' => 0), array(array('with'))
        ))));
        $this->assertEquals(7, $method->invokeArgs(null, array_by_ref(array(
            '#', array('usedFeature' => array('each' => 6), 'level' => 0, 'currentToken' => array(0,0,0,0,0,0,0,0), 'flags' => array('spvar' => 0, 'nohbh' => 0), 'elsechain' => false, 'elselvl' => array(), 'helperresolver' => 0), array(array('each'))
        ))));
        $this->assertEquals(8, $method->invokeArgs(null, array_by_ref(array(
            '#', array('usedFeature' => array('unless' => 7), 'level' => 0, 'currentToken' => array(0,0,0,0,0,0,0,0), 'flags' => array('spvar' => 0, 'nohbh' => 0), 'elsechain' => false, 'elselvl' => array(), 'helperresolver' => 0), array(array('unless'))
        ))));
        $this->assertEquals(9, $method->invokeArgs(null, array_by_ref(array(
            '#', array('helpers' => array('abc' => ''), 'usedFeature' => array('helper' => 8), 'level' => 0, 'currentToken' => array(0,0,0,0,0,0,0,0), 'flags' => array('spvar' => 0), 'elsechain' => false, 'elselvl' => array()), array(array('abc'))
        ))));
        $this->assertEquals(11, $method->invokeArgs(null, array_by_ref(array(
            '#', array('helpers' => array('abc' => ''), 'usedFeature' => array('helper' => 10), 'level' => 0, 'currentToken' => array(0,0,0,0,0,0,0,0), 'flags' => array('spvar' => 0), 'elsechain' => false, 'elselvl' => array()), array(array('abc'))
        ))));
        $this->assertEquals(true, $method->invokeArgs(null, array_by_ref(array(
            '>', array('partialresolver' => false, 'usedFeature' => array('partial' => 7), 'level' => 0, 'flags' => array('skippartial' => 0, 'runpart' => 0, 'spvar' => 0), 'currentToken' => array(0,0,0,0,0,0,0,0), 'elsechain' => false, 'elselvl' => array()), array('test')
        ))));
    }
}
