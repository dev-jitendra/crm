<?php

use LightnCandy\LightnCandy;
use LightnCandy\Runtime;
use LightnCandy\SafeString;
use PHPUnit\Framework\TestCase;

require_once(__DIR__ . '/test_util.php');

class CompilerTest extends TestCase
{
    
    public function testOn_getFuncName() {
        $method = new \ReflectionMethod('LightnCandy\Compiler', 'getFuncName');
        $method->setAccessible(true);
        $this->assertEquals('LR::test(', $method->invokeArgs(null, array_by_ref(array(
            array('flags' => array('standalone' => 0, 'debug' => 0), 'runtime' => 'Runtime', 'runtimealias' => 'LR'), 'test', ''
        ))));
        $this->assertEquals('LL::test2(', $method->invokeArgs(null, array_by_ref(array(
            array('flags' => array('standalone' => 0, 'debug' => 0), 'runtime' => 'Runtime', 'runtimealias' => 'LL'), 'test2', ''
        ))));
        $this->assertEquals("lala_abctest3(", $method->invokeArgs(null, array_by_ref(array(
            array('flags' => array('standalone' => 1, 'debug' => 0), 'runtime' => 'Runtime', 'runtimealias' => 0, 'funcprefix' => 'lala_abc'), 'test3', ''
        ))));
        $this->assertEquals('RR::debug(\'abc\', \'test\', ', $method->invokeArgs(null, array_by_ref(array(
            array('flags' => array('standalone' => 0, 'debug' => 1), 'runtime' => 'Runtime', 'runtimealias' => 'RR', 'funcprefix' => 'haha456'), 'test', 'abc'
        ))));
    }
    
    public function testOn_getVariableNames() {
        $method = new \ReflectionMethod('LightnCandy\Compiler', 'getVariableNames');
        $method->setAccessible(true);
        $this->assertEquals(array('array(array($in),array())', array('this')), $method->invokeArgs(null, array_by_ref(array(
            array('flags'=>array('spvar'=>true)), array(null)
        ))));
        $this->assertEquals(array('array(array($in,$in),array())', array('this', 'this')), $method->invokeArgs(null, array_by_ref(array(
            array('flags'=>array('spvar'=>true)), array(null, null)
        ))));
        $this->assertEquals(array('array(array(),array(\'a\'=>$in))', array('this')), $method->invokeArgs(null, array_by_ref(array(
            array('flags'=>array('spvar'=>true)), array('a' => null)
        ))));
    }
    
    public function testOn_getVariableName() {
        $method = new \ReflectionMethod('LightnCandy\Compiler', 'getVariableName');
        $method->setAccessible(true);
        $this->assertEquals(array('$in', 'this'), $method->invokeArgs(null, array_by_ref(array(
            array('flags'=>array('spvar'=>true,'debug'=>0)), array(null)
        ))));
        $this->assertEquals(array('(($inary && isset($in[\'true\'])) ? $in[\'true\'] : null)', '[true]'), $method->invokeArgs(null, array_by_ref(array(
            array('flags'=>array('spvar'=>true,'debug'=>0,'prop'=>0,'method'=>0,'mustlok'=>0,'mustlam'=>0,'lambda'=>0,'jslen'=>0)), array('true')
        ))));
        $this->assertEquals(array('(($inary && isset($in[\'false\'])) ? $in[\'false\'] : null)', '[false]'), $method->invokeArgs(null, array_by_ref(array(
            array('flags'=>array('spvar'=>true,'debug'=>0,'prop'=>0,'method'=>0,'mustlok'=>0,'mustlam'=>0,'lambda'=>0,'jslen'=>0)), array('false')
        ))));
        $this->assertEquals(array('true', 'true'), $method->invokeArgs(null, array_by_ref(array(
            array('flags'=>array('spvar'=>true,'debug'=>0)), array(-1, 'true')
        ))));
        $this->assertEquals(array('false', 'false'), $method->invokeArgs(null, array_by_ref(array(
            array('flags'=>array('spvar'=>true,'debug'=>0)), array(-1, 'false')
        ))));
        $this->assertEquals(array('(($inary && isset($in[\'2\'])) ? $in[\'2\'] : null)', '[2]'), $method->invokeArgs(null, array_by_ref(array(
            array('flags'=>array('spvar'=>true,'debug'=>0,'prop'=>0,'method'=>0,'mustlok'=>0,'mustlam'=>0,'lambda'=>0,'jslen'=>0)), array('2')
        ))));
        $this->assertEquals(array('2', '2'), $method->invokeArgs(null, array_by_ref(array(
            array('flags'=>array('spvar'=>true,'debug'=>0,'prop'=>0,'method'=>0)), array(-1, '2')
        ))));
        $this->assertEquals(array('(($inary && isset($in[\'@index\'])) ? $in[\'@index\'] : null)', '[@index]'), $method->invokeArgs(null, array_by_ref(array(
            array('flags'=>array('spvar'=>false,'debug'=>0,'prop'=>0,'method'=>0,'mustlok'=>0,'mustlam'=>0,'lambda'=>0,'jslen'=>0)), array('@index')
        ))));
        $this->assertEquals(array("(isset(\$cx['sp_vars']['index']) ? \$cx['sp_vars']['index'] : null)", '@[index]'), $method->invokeArgs(null, array_by_ref(array(
            array('flags'=>array('spvar'=>true,'debug'=>0,'prop'=>0,'method'=>0,'mustlok'=>0,'mustlam'=>0,'lambda'=>0,'jslen'=>0)), array('@index')
        ))));
        $this->assertEquals(array("(isset(\$cx['sp_vars']['key']) ? \$cx['sp_vars']['key'] : null)", '@[key]'), $method->invokeArgs(null, array_by_ref(array(
            array('flags'=>array('spvar'=>true,'debug'=>0,'prop'=>0,'method'=>0,'mustlok'=>0,'mustlam'=>0,'lambda'=>0,'jslen'=>0)), array('@key')
        ))));
        $this->assertEquals(array("(isset(\$cx['sp_vars']['first']) ? \$cx['sp_vars']['first'] : null)", '@[first]'), $method->invokeArgs(null, array_by_ref(array(
            array('flags'=>array('spvar'=>true,'debug'=>0,'prop'=>0,'method'=>0,'mustlok'=>0,'mustlam'=>0,'lambda'=>0,'jslen'=>0)), array('@first')
        ))));
        $this->assertEquals(array("(isset(\$cx['sp_vars']['last']) ? \$cx['sp_vars']['last'] : null)", '@[last]'), $method->invokeArgs(null, array_by_ref(array(
            array('flags'=>array('spvar'=>true,'debug'=>0,'prop'=>0,'method'=>0,'mustlok'=>0,'mustlam'=>0,'lambda'=>0,'jslen'=>0)), array('@last')
        ))));
        $this->assertEquals(array('(($inary && isset($in[\'"a"\'])) ? $in[\'"a"\'] : null)', '["a"]'), $method->invokeArgs(null, array_by_ref(array(
            array('flags'=>array('spvar'=>true,'debug'=>0,'prop'=>0,'method'=>0,'mustlok'=>0,'mustlam'=>0,'lambda'=>0,'jslen'=>0)), array('"a"')
        ))));
        $this->assertEquals(array('"a"', '"a"'), $method->invokeArgs(null, array_by_ref(array(
            array('flags'=>array('spvar'=>true,'debug'=>0)), array(-1, '"a"')
        ))));
        $this->assertEquals(array('(($inary && isset($in[\'a\'])) ? $in[\'a\'] : null)', '[a]'), $method->invokeArgs(null, array_by_ref(array(
            array('flags'=>array('spvar'=>true,'debug'=>0,'prop'=>0,'method'=>0,'mustlok'=>0,'mustlam'=>0,'lambda'=>0,'jslen'=>0)), array('a')
        ))));
        $this->assertEquals(array('((isset($cx[\'scopes\'][count($cx[\'scopes\'])-1]) && is_array($cx[\'scopes\'][count($cx[\'scopes\'])-1]) && isset($cx[\'scopes\'][count($cx[\'scopes\'])-1][\'a\'])) ? $cx[\'scopes\'][count($cx[\'scopes\'])-1][\'a\'] : null)', '../[a]'), $method->invokeArgs(null, array_by_ref(array(
            array('flags'=>array('spvar'=>true,'debug'=>0,'prop'=>0,'method'=>0,'mustlok'=>0,'mustlam'=>0,'lambda'=>0,'jslen'=>0)), array(1,'a')
        ))));
        $this->assertEquals(array('((isset($cx[\'scopes\'][count($cx[\'scopes\'])-3]) && is_array($cx[\'scopes\'][count($cx[\'scopes\'])-3]) && isset($cx[\'scopes\'][count($cx[\'scopes\'])-3][\'a\'])) ? $cx[\'scopes\'][count($cx[\'scopes\'])-3][\'a\'] : null)', '../../../[a]'), $method->invokeArgs(null, array_by_ref(array(
            array('flags'=>array('spvar'=>true,'debug'=>0,'prop'=>0,'method'=>0,'mustlok'=>0,'mustlam'=>0,'lambda'=>0,'jslen'=>0)), array(3,'a')
        ))));
        $this->assertEquals(array('(($inary && isset($in[\'id\'])) ? $in[\'id\'] : null)', 'this.[id]'), $method->invokeArgs(null, array_by_ref(array(
            array('flags'=>array('spvar'=>true,'debug'=>0,'prop'=>0,'method'=>0,'mustlok'=>0,'mustlam'=>0,'lambda'=>0,'jslen'=>0)), array(null, 'id')
        ))));
        $this->assertEquals(array('LR::v($cx, $in, isset($in) ? $in : null, array(\'id\'))', 'this.[id]'), $method->invokeArgs(null, array_by_ref(array(
            array('flags'=>array('prop'=>true,'spvar'=>true,'debug'=>0,'method'=>0,'mustlok'=>0,'mustlam'=>0,'lambda'=>0,'jslen'=>0,'standalone'=>0), 'runtime' => 'Runtime', 'runtimealias' => 'LR'), array(null, 'id')
        ))));
    }
    
    public function testOn_addUsageCount() {
        $method = new \ReflectionMethod('LightnCandy\Compiler', 'addUsageCount');
        $method->setAccessible(true);
        $this->assertEquals(1, $method->invokeArgs(null, array_by_ref(array(
            array('usedCount' => array('test' => array())), 'test', 'testname'
        ))));
        $this->assertEquals(3, $method->invokeArgs(null, array_by_ref(array(
            array('usedCount' => array('test' => array('testname' => 2))), 'test', 'testname'
        ))));
        $this->assertEquals(5, $method->invokeArgs(null, array_by_ref(array(
            array('usedCount' => array('test' => array('testname' => 2))), 'test', 'testname', 3
        ))));
    }
}
