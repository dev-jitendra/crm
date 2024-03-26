<?php


namespace chillerlan\QRCodeTest\Helpers;

use chillerlan\QRCode\Helpers\Polynomial;
use chillerlan\QRCode\QRCodeException;
use PHPUnit\Framework\TestCase;


final class PolynomialTest extends TestCase{

	protected Polynomial $polynomial;

	protected function setUp():void{
		$this->polynomial = new Polynomial;
	}

	public function testGexp():void{
		$this::assertSame(142, $this->polynomial->gexp(-1));
		$this::assertSame(133, $this->polynomial->gexp(128));
		$this::assertSame(2,   $this->polynomial->gexp(256));
	}

	public function testGlogException():void{
		$this->expectException(QRCodeException::class);
		$this->expectExceptionMessage('log(0)');

		$this->polynomial->glog(0);
	}
}
