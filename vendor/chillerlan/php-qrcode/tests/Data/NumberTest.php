<?php


namespace chillerlan\QRCodeTest\Data;

use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\Data\{Number, QRCodeDataException, QRDataInterface};


final class NumberTest extends DatainterfaceTestAbstract{

	
	protected string $testdata  = '0123456789';

	
	protected array $expected = [
		16, 40, 12, 86, 106, 105, 0, 236,
		17, 236, 17, 236, 17, 236, 17, 236,
		17, 236, 17, 236, 17, 236, 17, 236,
		17, 236, 17, 236, 17, 236, 17, 236,
		17, 236, 17, 236, 17, 236, 17, 236,
		17, 236, 17, 236, 17, 236, 17, 236,
		17, 236, 17, 236, 17, 236, 17, 236,
		17, 236, 17, 236, 17, 236, 17, 236,
		17, 236, 17, 236, 17, 236, 17, 236,
		17, 236, 17, 236, 17, 236, 17, 236,
		201, 141, 102, 116, 238, 162, 239, 230,
		222, 37, 79, 192, 42, 109, 188, 72,
		89, 63, 168, 151
	];

	
	protected function getDataInterfaceInstance(QROptions $options):QRDataInterface{
		return new Number($options);
	}

	
	public function testGetCharCodeException():void{
		$this->expectException(QRCodeDataException::class);
		$this->expectExceptionMessage('illegal char: "#" [35]');

		$this->dataInterface->setData('#');
	}

}
