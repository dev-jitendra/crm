<?php


namespace chillerlan\QRCodeTest\Data;

use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\Data\{Byte, MaskPatternTester};
use PHPUnit\Framework\TestCase;


final class MaskPatternTesterTest extends TestCase{

	
	public function testMaskpattern():void{
		$dataInterface = new Byte(new QROptions(['version' => 10]), 'test');

		$this::assertSame(3, (new MaskPatternTester($dataInterface))->getBestMaskPattern());
	}

	
	public function testMaskpatternID():void{
		$dataInterface = new Byte(new QROptions(['version' => 10]), 'test');

		$this::assertSame(4243, (new MaskPatternTester($dataInterface))->testPattern(3));
	}

}
