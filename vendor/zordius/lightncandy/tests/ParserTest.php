<?php

use LightnCandy\LightnCandy;
use LightnCandy\Runtime;
use LightnCandy\SafeString;
use PHPUnit\Framework\TestCase;

require_once(__DIR__ . '/test_util.php');

class ParserTest extends TestCase
{
    
    public function testOn_getExpression() {
        $method = new \ReflectionMethod('LightnCandy\Parser', 'getExpression');
        $method->setAccessible(true);
        $this->assertEquals(array('this'), $method->invokeArgs(null, array_by_ref(array(
            'this', array('flags' => array('strpar' => 0, 'advar' => 0, 'this' => 0)), 0
        ))));
        $this->assertEquals(array(), $method->invokeArgs(null, array_by_ref(array(
            'this', array('flags' => array('strpar' => 0, 'advar' => 0, 'this' => 1)), 0
        ))));
        $this->assertEquals(array(1), $method->invokeArgs(null, array_by_ref(array(
            '..', array('flags' => array('strpar' => 0, 'advar' => 0, 'this' => 1, 'parent' => 1), 'usedFeature' => array('parent' => 0)), 0
        ))));
        $this->assertEquals(array(1), $method->invokeArgs(null, array_by_ref(array(
            '../', array('flags' => array('strpar' => 0, 'advar' => 0, 'this' => 1, 'parent' => 1), 'usedFeature' => array('parent' => 0)), 0
        ))));
        $this->assertEquals(array(1), $method->invokeArgs(null, array_by_ref(array(
            '../.', array('flags' => array('strpar' => 0, 'advar' => 0, 'this' => 1, 'parent' => 1), 'usedFeature' => array('parent' => 0)), 0
        ))));
        $this->assertEquals(array(1), $method->invokeArgs(null, array_by_ref(array(
            '../this', array('flags' => array('strpar' => 0, 'advar' => 0, 'this' => 1, 'parent' => 1), 'usedFeature' => array('parent' => 0)), 0
        ))));
        $this->assertEquals(array(1, 'a'), $method->invokeArgs(null, array_by_ref(array(
            '../a', array('flags' => array('strpar' => 0, 'advar' => 0, 'this' => 1, 'parent' => 1), 'usedFeature' => array('parent' => 0)), 0
        ))));
        $this->assertEquals(array(2, 'a', 'b'), $method->invokeArgs(null, array_by_ref(array(
            '../../a.b', array('flags' => array('strpar' => 0, 'advar' => 0, 'this' => 0, 'parent' => 1), 'usedFeature' => array('parent' => 0)), 0
        ))));
        $this->assertEquals(array(2, '[a]', 'b'), $method->invokeArgs(null, array_by_ref(array(
            '../../[a].b', array('flags' => array('strpar' => 0, 'advar' => 0, 'this' => 0, 'parent' => 1), 'usedFeature' => array('parent' => 0)), 0
        ))));
        $this->assertEquals(array(2, 'a', 'b'), $method->invokeArgs(null, array_by_ref(array(
            '../../[a].b', array('flags' => array('strpar' => 0, 'advar' => 1, 'this' => 0, 'parent' => 1), 'usedFeature' => array('parent' => 0)), 0
        ))));
        $this->assertEquals(array(0, 'id'), $method->invokeArgs(null, array_by_ref(array(
            'this.id', array('flags' => array('strpar' => 0, 'advar' => 1, 'this' => 1, 'parent' => 1), 'usedFeature' => array('parent' => 0)), 0
        ))));
        $this->assertEquals(array('this', 'id'), $method->invokeArgs(null, array_by_ref(array(
            'this.id', array('flags' => array('strpar' => 0, 'advar' => 1, 'this' => 0, 'parent' => 1), 'usedFeature' => array('parent' => 0)), 0
        ))));
        $this->assertEquals(array(0, 'id'), $method->invokeArgs(null, array_by_ref(array(
            './id', array('flags' => array('strpar' => 0, 'advar' => 1, 'this' => 0, 'parent' => 1), 'usedFeature' => array('parent' => 0)), 0
        ))));
        $this->assertEquals(array(\LightnCandy\Parser::LITERAL, '\'a.b\''), $method->invokeArgs(null, array_by_ref(array(
            '"a.b"', array('flags' => array('strpar' => 0, 'advar' => 1, 'this' => 0, 'parent' => 1), 'usedFeature' => array('parent' => 0)), 1
        ))));
        $this->assertEquals(array(\LightnCandy\Parser::LITERAL, '123'), $method->invokeArgs(null, array_by_ref(array(
            '123', array('flags' => array('strpar' => 0, 'advar' => 1, 'this' => 0, 'parent' => 1), 'usedFeature' => array('parent' => 0)), 1
        ))));
        $this->assertEquals(array(\LightnCandy\Parser::LITERAL, 'null'), $method->invokeArgs(null, array_by_ref(array(
            'null', array('flags' => array('strpar' => 0, 'advar' => 1, 'this' => 0, 'parent' => 1), 'usedFeature' => array('parent' => 0)), 1
        ))));
    }
    
    public function testOn_parse() {
        $method = new \ReflectionMethod('LightnCandy\Parser', 'parse');
        $this->assertEquals(array(false, array(array())), $method->invokeArgs(null, array_by_ref(array(
            array(0,0,0,0,0,0,0,''), array('flags' => array('strpar' => 0, 'advar' => 0, 'this' => 1, 'namev' => 0, 'noesc' => 0), 'rawblock' => false)
        ))));
        $this->assertEquals(array(true, array(array())), $method->invokeArgs(null, array_by_ref(array(
            array(0,0,0,'{{',0,'{',0,''), array('flags' => array('strpar' => 0, 'advar' => 0, 'this' => 1, 'namev' => 0, 'noesc' => 0), 'rawblock' => false)
        ))));
        $this->assertEquals(array(true, array(array())), $method->invokeArgs(null, array_by_ref(array(
            array(0,0,0,0,0,0,0,''), array('flags' => array('strpar' => 0, 'advar' => 0, 'this' => 1, 'namev' => 0, 'noesc' => 1), 'rawblock' => false)
        ))));
        $this->assertEquals(array(false, array(array('a'))), $method->invokeArgs(null, array_by_ref(array(
            array(0,0,0,0,0,0,0,'a'), array('flags' => array('strpar' => 0, 'advar' => 0, 'this' => 1, 'namev' => 0, 'noesc' => 0), 'rawblock' => false)
        ))));
        $this->assertEquals(array(false, array(array('a'), array('b'))), $method->invokeArgs(null, array_by_ref(array(
            array(0,0,0,0,0,0,0,'a  b'), array('flags' => array('strpar' => 0, 'advar' => 0, 'this' => 1, 'namev' => 0, 'noesc' => 0), 'rawblock' => false)
        ))));
        $this->assertEquals(array(false, array(array('a'), array('"b'), array('c"'))), $method->invokeArgs(null, array_by_ref(array(
            array(0,0,0,0,0,0,0,'a "b c"'), array('flags' => array('strpar' => 0, 'advar' => 0, 'this' => 1, 'namev' => 0, 'noesc' => 0), 'rawblock' => false)
        ))));
        $this->assertEquals(array(false, array(array('a'), array(-1, '\'b c\''))), $method->invokeArgs(null, array_by_ref(array(
            array(0,0,0,0,0,0,0,'a "b c"'), array('flags' => array('strpar' => 0, 'advar' => 1, 'this' => 1, 'namev' => 0, 'noesc' => 0), 'rawblock' => false)
        ))));
        $this->assertEquals(array(false, array(array('a'), array('[b'), array('c]'))), $method->invokeArgs(null, array_by_ref(array(
            array(0,0,0,0,0,0,0,'a [b c]'), array('flags' => array('strpar' => 0, 'advar' => 0, 'this' => 1, 'namev' => 0, 'noesc' => 0), 'rawblock' => false)
        ))));
        $this->assertEquals(array(false, array(array('a'), array('[b'), array('c]'))), $method->invokeArgs(null, array_by_ref(array(
            array(0,0,0,0,0,0,0,'a [b c]'), array('flags' => array('strpar' => 0, 'advar' => 0, 'this' => 1, 'namev' => 1, 'noesc' => 0), 'rawblock' => false)
        ))));
        $this->assertEquals(array(false, array(array('a'), array('b c'))), $method->invokeArgs(null, array_by_ref(array(
            array(0,0,0,0,0,0,0,'a [b c]'), array('flags' => array('strpar' => 0, 'advar' => 1, 'this' => 1, 'namev' => 0, 'noesc' => 0), 'rawblock' => false)
        ))));
        $this->assertEquals(array(false, array(array('a'), array('b c'))), $method->invokeArgs(null, array_by_ref(array(
            array(0,0,0,0,0,0,0,'a [b c]'), array('flags' => array('strpar' => 0, 'advar' => 1, 'this' => 1, 'namev' => 1, 'noesc' => 0), 'rawblock' => false)
        ))));
        $this->assertEquals(array(false, array(array('a'), 'q' => array('b c'))), $method->invokeArgs(null, array_by_ref(array(
            array(0,0,0,0,0,0,0,'a q=[b c]'), array('flags' => array('strpar' => 0, 'advar' => 1, 'this' => 1, 'namev' => 1, 'noesc' => 0), 'rawblock' => false)
        ))));
        $this->assertEquals(array(false, array(array('a'), array('q=[b c'))), $method->invokeArgs(null, array_by_ref(array(
            array(0,0,0,0,0,0,0,'a [q=[b c]'), array('flags' => array('strpar' => 0, 'advar' => 1, 'this' => 1, 'namev' => 1, 'noesc' => 0), 'rawblock' => false)
        ))));
        $this->assertEquals(array(false, array(array('a'), 'q' => array('[b'), array('c]'))), $method->invokeArgs(null, array_by_ref(array(
            array(0,0,0,0,0,0,0,'a q=[b c]'), array('flags' => array('strpar' => 0, 'advar' => 0, 'this' => 1, 'namev' => 1, 'noesc' => 0), 'rawblock' => false)
        ))));
        $this->assertEquals(array(false, array(array('a'), 'q' => array('b'), array('c'))), $method->invokeArgs(null, array_by_ref(array(
            array(0,0,0,0,0,0,0,'a [q]=b c'), array('flags' => array('strpar' => 0, 'advar' => 0, 'this' => 1, 'namev' => 1, 'noesc' => 0), 'rawblock' => false)
        ))));
        $this->assertEquals(array(false, array(array('a'), 'q' => array(-1, '\'b c\''))), $method->invokeArgs(null, array_by_ref(array(
            array(0,0,0,0,0,0,0,'a q="b c"'), array('flags' => array('strpar' => 0, 'advar' => 1, 'this' => 1, 'namev' => 1, 'noesc' => 0), 'rawblock' => false)
        ))));
        $this->assertEquals(array(false, array(array(-2, array(array('foo'), array('bar')), '(foo bar)'))), $method->invokeArgs(null, array_by_ref(array(
            array(0,0,0,0,0,0,0,'(foo bar)'), array('flags' => array('strpar' => 0, 'advar' => 1, 'this' => 1, 'namev' => 1, 'noesc' => 0, 'exhlp' => 1, 'lambda' => 0), 'ops' => array('seperator' => ''), 'usedFeature' => array('subexp' => 0), 'rawblock' => false)
        ))));
        $this->assertEquals(array(false, array(array('foo'), array("'=='"), array('bar'))), $method->invokeArgs(null, array_by_ref(array(
            array(0,0,0,0,0,0,0,"foo '==' bar"), array('flags' => array('strpar' => 0, 'advar' => 1, 'namev' => 1, 'noesc' => 0, 'this' => 0), 'rawblock' => false)
        ))));
        $this->assertEquals(array(false, array(array(-2, array(array('foo'), array('bar')), '( foo bar)'))), $method->invokeArgs(null, array_by_ref(array(
            array(0,0,0,0,0,0,0,'( foo bar)'), array('flags' => array('strpar' => 0, 'advar' => 1, 'this' => 1, 'namev' => 1, 'noesc' => 0, 'exhlp' => 1, 'lambda' => 0), 'ops' => array('seperator' => ''), 'usedFeature' => array('subexp' => 0), 'rawblock' => false)
        ))));
        $this->assertEquals(array(false, array(array('a'), array(-1, '\' b c\''))), $method->invokeArgs(null, array_by_ref(array(
            array(0,0,0,0,0,0,0,'a " b c"'), array('flags' => array('strpar' => 0, 'advar' => 1, 'this' => 1, 'namev' => 0, 'noesc' => 0), 'rawblock' => false)
        ))));
        $this->assertEquals(array(false, array(array('a'), 'q' => array(-1, '\' b c\''))), $method->invokeArgs(null, array_by_ref(array(
            array(0,0,0,0,0,0,0,'a q=" b c"'), array('flags' => array('strpar' => 0, 'advar' => 1, 'this' => 1, 'namev' => 1, 'noesc' => 0), 'rawblock' => false)
        ))));
        $this->assertEquals(array(false, array(array('foo'), array(-1, "' =='"), array('bar'))), $method->invokeArgs(null, array_by_ref(array(
            array(0,0,0,0,0,0,0,"foo \' ==\' bar"), array('flags' => array('strpar' => 0, 'advar' => 1, 'namev' => 1, 'noesc' => 0, 'this' => 0), 'rawblock' => false)
        ))));
        $this->assertEquals(array(false, array(array('a'), array(' b c'))), $method->invokeArgs(null, array_by_ref(array(
            array(0,0,0,0,0,0,0,'a [ b c]'), array('flags' => array('strpar' => 0, 'advar' => 1, 'this' => 1, 'namev' => 1, 'noesc' => 0), 'rawblock' => false)
        ))));
        $this->assertEquals(array(false, array(array('a'), 'q' => array(-1, "' d e'"))), $method->invokeArgs(null, array_by_ref(array(
            array(0,0,0,0,0,0,0,"a q=\' d e\'"), array('flags' => array('strpar' => 0, 'advar' => 1, 'this' => 1, 'namev' => 1, 'noesc' => 0), 'rawblock' => false)
        ))));
        $this->assertEquals(array(false, array('q' => array(-2, array(array('foo'), array('bar')), '( foo bar)'))), $method->invokeArgs(null, array_by_ref(array(
            array(0,0,0,0,0,0,0,'q=( foo bar)'), array('flags' => array('strpar' => 0, 'advar' => 1, 'this' => 1, 'namev' => 1, 'noesc' => 0, 'exhlp' => 0, 'lambda' => 0), 'usedFeature' => array('subexp' => 0), 'ops' => array('seperator' => 0), 'rawblock' => false, 'helperresolver' => 0)
        ))));
        $this->assertEquals(array(false, array(array('foo'))), $method->invokeArgs(null, array_by_ref(array(
            array(0,0,0,0,0,0,'>','foo'), array('flags' => array('strpar' => 0, 'advar' => 1, 'this' => 1, 'namev' => 1, 'noesc' => 0, 'exhlp' => 0, 'lambda' => 0), 'usedFeature' => array('subexp' => 0), 'ops' => array('seperator' => 0), 'rawblock' => false)
        ))));
        $this->assertEquals(array(false, array(array('foo'))), $method->invokeArgs(null, array_by_ref(array(
            array(0,0,0,0,0,0,'>','"foo"'), array('flags' => array('strpar' => 0, 'advar' => 1, 'this' => 1, 'namev' => 1, 'noesc' => 0, 'exhlp' => 0, 'lambda' => 0), 'usedFeature' => array('subexp' => 0), 'ops' => array('seperator' => 0), 'rawblock' => false)
        ))));
        $this->assertEquals(array(false, array(array('foo'))), $method->invokeArgs(null, array_by_ref(array(
            array(0,0,0,0,0,0,'>','[foo] '), array('flags' => array('strpar' => 0, 'advar' => 1, 'this' => 1, 'namev' => 1, 'noesc' => 0, 'exhlp' => 0, 'lambda' => 0), 'usedFeature' => array('subexp' => 0), 'ops' => array('seperator' => 0), 'rawblock' => false)
        ))));
        $this->assertEquals(array(false, array(array('foo'))), $method->invokeArgs(null, array_by_ref(array(
            array(0,0,0,0,0,0,'>','\\\'foo\\\''), array('flags' => array('strpar' => 0, 'advar' => 1, 'this' => 1, 'namev' => 1, 'noesc' => 0, 'exhlp' => 0, 'lambda' => 0), 'usedFeature' => array('subexp' => 0), 'ops' => array('seperator' => 0), 'rawblock' => false)
        ))));
    }
    
    public function testOn_getPartialName() {
        $method = new \ReflectionMethod('LightnCandy\Parser', 'getPartialName');
        $this->assertEquals(null, $method->invokeArgs(null, array_by_ref(array(
            array()
        ))));
        $this->assertEquals(array('foo'), $method->invokeArgs(null, array_by_ref(array(
            array('foo')
        ))));
        $this->assertEquals(array('foo'), $method->invokeArgs(null, array_by_ref(array(
            array('"foo"')
        ))));
        $this->assertEquals(array('foo'), $method->invokeArgs(null, array_by_ref(array(
            array('[foo]')
        ))));
        $this->assertEquals(array('foo'), $method->invokeArgs(null, array_by_ref(array(
            array("\\'foo\\'")
        ))));
        $this->assertEquals(array('foo'), $method->invokeArgs(null, array_by_ref(array(
            array(0, 'foo'), 1
        ))));
    }
    
    public function testOn_subexpression() {
        $method = new \ReflectionMethod('LightnCandy\Parser', 'subexpression');
        $this->assertEquals(array(\LightnCandy\Parser::SUBEXP, array(array('a'), array('b')), '(a b)'), $method->invokeArgs(null, array_by_ref(array(
            '(a b)', array('usedFeature' => array('subexp' => 0), 'flags' => array('advar' => 0, 'namev' => 0, 'this' => 0, 'exhlp' => 1, 'strpar' => 0))
        ))));
    }
    
    public function testOn_isSubExp() {
        $method = new \ReflectionMethod('LightnCandy\Parser', 'isSubExp');
        $this->assertEquals(false, $method->invokeArgs(null, array_by_ref(array(
            0
        ))));
        $this->assertEquals(false, $method->invokeArgs(null, array_by_ref(array(
            array()
        ))));
        $this->assertEquals(false, $method->invokeArgs(null, array_by_ref(array(
            array(\LightnCandy\Parser::SUBEXP, 0)
        ))));
        $this->assertEquals(false, $method->invokeArgs(null, array_by_ref(array(
            array(\LightnCandy\Parser::SUBEXP, 0, 0)
        ))));
        $this->assertEquals(false, $method->invokeArgs(null, array_by_ref(array(
            array(\LightnCandy\Parser::SUBEXP, 0, '', 0)
        ))));
        $this->assertEquals(true, $method->invokeArgs(null, array_by_ref(array(
            array(\LightnCandy\Parser::SUBEXP, 0, '')
        ))));
    }
    
    public function testOn_advancedVariable() {
        $method = new \ReflectionMethod('LightnCandy\Parser', 'advancedVariable');
        $method->setAccessible(true);
        $this->assertEquals(array(array('this')), $method->invokeArgs(null, array_by_ref(array(
            array('this'), array('flags' => array('advar' => 1, 'namev' => 1, 'this' => 0,)), 0
        ))));
        $this->assertEquals(array(array()), $method->invokeArgs(null, array_by_ref(array(
            array('this'), array('flags' => array('advar' => 1, 'namev' => 1, 'this' => 1)), 0
        ))));
        $this->assertEquals(array(array('a')), $method->invokeArgs(null, array_by_ref(array(
            array('a'), array('flags' => array('advar' => 1, 'namev' => 1, 'this' => 0, 'strpar' => 0)), 0
        ))));
        $this->assertEquals(array(array('a'), array('b')), $method->invokeArgs(null, array_by_ref(array(
            array('a', 'b'), array('flags' => array('advar' => 1, 'namev' => 1, 'this' => 0, 'strpar' => 0)), 0
        ))));
        $this->assertEquals(array('a' => array('b')), $method->invokeArgs(null, array_by_ref(array(
            array('a=b'), array('flags' => array('advar' => 1, 'namev' => 1, 'this' => 0, 'strpar' => 0)), 0
        ))));
        $this->assertEquals(array('fo o' => array(\LightnCandy\Parser::LITERAL, '123')), $method->invokeArgs(null, array_by_ref(array(
            array('[fo o]=123'), array('flags' => array('advar' => 1, 'namev' => 1, 'this' => 0)), 0
        ))));
        $this->assertEquals(array('fo o' => array(\LightnCandy\Parser::LITERAL, '\'bar\'')), $method->invokeArgs(null, array_by_ref(array(
            array('[fo o]="bar"'), array('flags' => array('advar' => 1, 'namev' => 1, 'this' => 0)), 0
        ))));
    }
    
    public function testOn_analyze() {
        $method = new \ReflectionMethod('LightnCandy\Parser', 'analyze');
        $method->setAccessible(true);
        $this->assertEquals(array('foo', 'bar'), $method->invokeArgs(null, array_by_ref(array(
            'foo bar', array('flags' => array('advar' => 1))
        ))));
        $this->assertEquals(array('foo', "'bar'"), $method->invokeArgs(null, array_by_ref(array(
            "foo 'bar'", array('flags' => array('advar' => 1))
        ))));
        $this->assertEquals(array('[fo o]', '"bar"'), $method->invokeArgs(null, array_by_ref(array(
            '[fo o] "bar"', array('flags' => array('advar' => 1))
        ))));
        $this->assertEquals(array('fo=123', 'bar="45', '6"'), $method->invokeArgs(null, array_by_ref(array(
            'fo=123 bar="45 6"', array('flags' => array('advar' => 0))
        ))));
        $this->assertEquals(array('fo=123', 'bar="45 6"'), $method->invokeArgs(null, array_by_ref(array(
            'fo=123 bar="45 6"', array('flags' => array('advar' => 1))
        ))));
        $this->assertEquals(array('[fo', 'o]=123'), $method->invokeArgs(null, array_by_ref(array(
            '[fo o]=123', array('flags' => array('advar' => 0))
        ))));
        $this->assertEquals(array('[fo o]=123'), $method->invokeArgs(null, array_by_ref(array(
            '[fo o]=123', array('flags' => array('advar' => 1))
        ))));
        $this->assertEquals(array('[fo o]=123', 'bar="456"'), $method->invokeArgs(null, array_by_ref(array(
            '[fo o]=123 bar="456"', array('flags' => array('advar' => 1))
        ))));
        $this->assertEquals(array('[fo o]="1 2 3"'), $method->invokeArgs(null, array_by_ref(array(
            '[fo o]="1 2 3"', array('flags' => array('advar' => 1))
        ))));
        $this->assertEquals(array('foo', 'a=(foo a=(foo a="ok"))'), $method->invokeArgs(null, array_by_ref(array(
            'foo a=(foo a=(foo a="ok"))', array('flags' => array('advar' => 1))
        ))));
    }
}
