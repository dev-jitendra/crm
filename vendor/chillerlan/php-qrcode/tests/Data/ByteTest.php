<?php


namespace chillerlan\QRCodeTest\Data;

use chillerlan\QRCode\Data\Byte;
use chillerlan\QRCode\Data\QRDataInterface;
use chillerlan\QRCode\QROptions;


final class ByteTest extends DatainterfaceTestAbstract{

	
	protected string $testdata = '[¯\_(ツ)_/¯]';

	
	protected array  $expected = [
		64, 245, 188, 42, 245, 197, 242, 142,
		56, 56, 66, 149, 242, 252, 42, 245,
		208, 236, 17, 236, 17, 236, 17, 236,
		17, 236, 17, 236, 17, 236, 17, 236,
		17, 236, 17, 236, 17, 236, 17, 236,
		17, 236, 17, 236, 17, 236, 17, 236,
		17, 236, 17, 236, 17, 236, 17, 236,
		17, 236, 17, 236, 17, 236, 17, 236,
		17, 236, 17, 236, 17, 236, 17, 236,
		17, 236, 17, 236, 17, 236, 17, 236,
		79, 89, 226, 48, 209, 89, 151, 1,
		12, 73, 42, 163, 11, 34, 255, 205,
		21, 47, 250, 101
	];

	
	protected function getDataInterfaceInstance(QROptions $options):QRDataInterface{
		return new Byte($options);
	}

}
