<?php
namespace ZBateson\MbWrapper;

use PHPUnit\Framework\TestCase;


class MbWrapperTest extends TestCase
{
    public function testMbCharsetConversion()
    {
        $arr = array_unique(MbWrapper::$mbAliases);
        $converter = new MbWrapper();
        $first = reset($arr);
        $test = $converter->convert('This is my string', 'UTF-8', $first);
        foreach ($arr as $dest) {
            $this->assertEquals(
                $test,
                $converter->convert($converter->convert($test, $first, $dest), $dest, $first)
            );
        }
    }

    public function testIconvCharsetConversion()
    {
        $arr = array_unique(MbWrapper::$iconvAliases);
        $converter = new MbWrapper();
        $first = reset($arr);
        $test = $converter->convert('This is my string', 'UTF-8', $first);
        foreach ($arr as $dest) {
            $this->assertEquals($test, $converter->convert($converter->convert($test, $first, $dest), $dest, $first));
        }
    }

    public function testMbConversionWithEmptyString()
    {
        $converter = new MbWrapper();
        $cs = reset(MbWrapper::$mbAliases);
        $this->assertEmpty($converter->convert('', 'UTF-8', $cs));
    }

    public function testIconvConversionWithEmptyString()
    {
        $converter = new MbWrapper();
        $cs = reset(MbWrapper::$iconvAliases);
        $this->assertEmpty($converter->convert('', 'UTF-8', $cs));
    }

    public function testMbIconvMixedCharsetConversion()
    {
        $mbArr = array_unique(MbWrapper::$mbAliases);
        $iconvArr = array_unique(MbWrapper::$iconvAliases);
        $converter = new MbWrapper();

        $mb = reset($mbArr);
        $iconv = reset($iconvArr);

        $testMb = $converter->convert('This is my string', 'UTF-8', $mb);
        $testIconv = $converter->convert('This is my string', 'UTF-8', $iconv);

        foreach ($iconvArr as $dest) {
            $this->assertEquals($testMb, $converter->convert($converter->convert($testMb, $mb, $dest), $dest, $mb));
        }
        foreach ($mbArr as $dest) {
            $this->assertEquals($testIconv, $converter->convert($converter->convert($testIconv, $iconv, $dest), $dest, $iconv));
        }
    }

    public function testSetCharsetConversions()
    {
        $arr = [
            'ISO-8859-8-I',
            'WINDOWS-1254',
            'CSPC-850-MULTILINGUAL',
            'GB18030_2000',
            'ISO_IR_157',
            'CS-ISO-LATIN-4',
            'ISO_IR_100',
            'WINDOWS-&#&#1254',
            'UTF-#@*(@8',
            'ISO-&@(#IR166'
        ];
        $test = 'This is my string';
        $converter = new MbWrapper();
        foreach ($arr as $dest) {
            $this->assertEquals($test, $converter->convert($converter->convert($test, 'UTF-8', $dest), $dest, 'UTF-8'), "Testing with $dest");
        }
    }

    public function testMbStrlen()
    {
        $arr = array_unique(MbWrapper::$mbAliases);
        $converter = new MbWrapper();
        $str = 'هلا والله بالغالي';
        $len = mb_strlen($str, 'UTF-8');
        $first = reset($arr);
        $test = $converter->convert($str, 'UTF-8', $first);
        foreach ($arr as $dest) {
            $this->assertEquals($len, $converter->getLength($converter->convert($test, $first, $dest), $dest));
        }
    }

    public function testIconvStrlen()
    {
        $arr = array_unique(MbWrapper::$iconvAliases);
        $converter = new MbWrapper();
        $str = 'هلا والله بالغالي';
        $len = mb_strlen($str, 'UTF-8');
        $first = reset($arr);
        $test = $converter->convert($str, 'UTF-8', $first);
        foreach ($arr as $dest) {
            $this->assertEquals($len, $converter->getLength($converter->convert($test, $first, $dest), $dest));
        }
    }

    public function testMbSubstr()
    {
        $arr = array_unique(MbWrapper::$mbAliases);
        $converter = new MbWrapper();
        $str = 'Needs to be simple';
        $len = mb_strlen($str, 'UTF-8');
        $first = reset($arr);
        $test = $converter->convert($str, 'UTF-8', $first);
        foreach ($arr as $dest) {
            $testConv = $converter->convert($test, $first, $dest);
            for ($i = 0; $i < $len; ++$i) {
                for ($j = $i + 1; $j <= $len; ++$j) {
                    $this->assertEquals(
                        mb_substr($str, $i, $j),
                        $converter->convert($converter->getSubstr($testConv, $dest, $i, $j), $dest, 'UTF-8'),
                        "Failed on $i iteration $j with " . $converter->convert($testConv, $dest, 'UTF-8')
                    );
                }
                $this->assertEquals(
                    mb_substr($str, $i),
                    $converter->convert($converter->getSubstr($testConv, $dest, $i), $dest, 'UTF-8')
                );
            }

        }
    }

    public function testIconvSubstr()
    {
        $arr = array_unique(MbWrapper::$iconvAliases);
        $converter = new MbWrapper();
        $str = 'Needs to be simple';
        $len = mb_strlen($str, 'UTF-8');
        $first = reset($arr);
        $test = $converter->convert($str, 'UTF-8', $first);

        
        

        foreach ($arr as $dest) {
            $testConv = $converter->convert($test, $first, $dest);
            for ($i = 0; $i < $len; ++$i) {
                for ($j = $i + 1; $j <= $len; ++$j) {
                    $this->assertEquals(
                        mb_substr($str, $i, $j),
                        $converter->convert($converter->getSubstr($testConv, $dest, $i, $j), $dest, 'UTF-8'),
                        "Failed on $i iteration $j with " . $converter->convert($testConv, $dest, 'UTF-8')
                    );
                }
                $this->assertEquals(
                    mb_substr($str, $i),
                    $converter->convert($converter->getSubstr($testConv, $dest, $i), $dest, 'UTF-8')
                );
            }

        }
    }
}
