<?php


namespace chillerlan\QRCodeTest\Data;

use chillerlan\QRCode\Data\{AlphaNum, QRCodeDataException, QRDataInterface};
use chillerlan\QRCode\QROptions;


final class AlphaNumTest extends DatainterfaceTestAbstract{

	
	protected string $testdata  = '0 $%*+-./:';

	
	protected array  $expected  = [
		32, 80, 36, 212, 252, 15, 175, 251,
		176, 236, 17, 236, 17, 236, 17, 236,
		17, 236, 17, 236, 17, 236, 17, 236,
		17, 236, 17, 236, 17, 236, 17, 236,
		17, 236, 17, 236, 17, 236, 17, 236,
		17, 236, 17, 236, 17, 236, 17, 236,
		17, 236, 17, 236, 17, 236, 17, 236,
		17, 236, 17, 236, 17, 236, 17, 236,
		17, 236, 17, 236, 17, 236, 17, 236,
		17, 236, 17, 236, 17, 236, 17, 236,
		112, 43, 9, 248, 200, 194, 75, 25,
		205, 173, 154, 68, 191, 16, 128,
		92, 112, 20, 198, 27
	];

	
	protected function getDataInterfaceInstance(QROptions $options):QRDataInterface{
		return new AlphaNum($options);
	}

	
	public function testGetCharCodeException():void{
		$this->expectException(QRCodeDataException::class);
		$this->expectExceptionMessage('illegal char: "#" [35]');

		$this->dataInterface->setData('#');
	}

}
