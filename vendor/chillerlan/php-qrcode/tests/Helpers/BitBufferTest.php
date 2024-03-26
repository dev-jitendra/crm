<?php


namespace chillerlan\QRCodeTest\Helpers;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\Helpers\BitBuffer;
use PHPUnit\Framework\TestCase;


final class BitBufferTest extends TestCase{

	protected BitBuffer $bitBuffer;

	protected function setUp():void{
		$this->bitBuffer = new BitBuffer;
	}

	public function bitProvider():array{
		return [
			'number'   => [QRCode::DATA_NUMBER, 16],
			'alphanum' => [QRCode::DATA_ALPHANUM, 32],
			'byte'     => [QRCode::DATA_BYTE, 64],
			'kanji'    => [QRCode::DATA_KANJI, 128],
		];
	}

	
	public function testPut(int $data, int $value):void{
		$this->bitBuffer->put($data, 4);
		$this::assertSame($value, $this->bitBuffer->getBuffer()[0]);
		$this::assertSame(4, $this->bitBuffer->getLength());
	}

	public function testClear():void{
		$this->bitBuffer->clear();
		$this::assertSame([], $this->bitBuffer->getBuffer());
		$this::assertSame(0, $this->bitBuffer->getLength());
	}

}
