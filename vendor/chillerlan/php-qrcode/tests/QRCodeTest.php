<?php


namespace chillerlan\QRCodeTest;

use chillerlan\QRCode\{QROptions, QRCode};
use chillerlan\QRCode\Data\{AlphaNum, Byte, Kanji, Number, QRCodeDataException};
use chillerlan\QRCode\Output\QRCodeOutputException;
use PHPUnit\Framework\TestCase;

use function random_bytes;


class QRCodeTest extends TestCase{

	
	protected QRCode $qrcode;
	
	protected QROptions $options;

	
	protected function setUp():void{
		$this->qrcode  = new QRCode;
		$this->options = new QROptions;
	}

	
	public function testIsNumber():void{
		$this::assertTrue($this->qrcode->isNumber('0123456789'));

		$this::assertFalse($this->qrcode->isNumber('ABC123'));
	}

	
	public function testIsAlphaNum():void{
		$this::assertTrue($this->qrcode->isAlphaNum('ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890 $%*+-./:'));

		$this::assertFalse($this->qrcode->isAlphaNum('abc'));
	}

	
	public function testIsKanji():void{
		$this::assertTrue($this->qrcode->isKanji('茗荷'));

		$this::assertFalse($this->qrcode->isKanji('Ã'));
		$this::assertFalse($this->qrcode->isKanji('ABC'));
		$this::assertFalse($this->qrcode->isKanji('123'));
	}

	
	public function testIsByte():void{
		$this::assertTrue($this->qrcode->isByte("\x01\x02\x03"));
		$this::assertTrue($this->qrcode->isByte('            ')); 
		$this::assertTrue($this->qrcode->isByte('0'));

		$this::assertFalse($this->qrcode->isByte(''));
	}

	
	public function testInitDataInterfaceException():void{
		$this->expectException(QRCodeOutputException::class);
		$this->expectExceptionMessage('invalid output type');

		$this->options->outputType = 'foo';

		(new QRCode($this->options))->render('test');
	}

	
	public function testGetMatrixException():void{
		$this->expectException(QRCodeDataException::class);
		$this->expectExceptionMessage('QRCode::getMatrix() No data given.');

		$this->qrcode->getMatrix('');
	}

	
	public function testAvoidTrimming():void{
		$m1 = $this->qrcode->getMatrix('hello')->matrix();
		$m2 = $this->qrcode->getMatrix('hello ')->matrix(); 

		$this::assertNotSame($m1, $m2);
	}

	
	public function testDataModeOverride():void{

		
		$this->options->dataModeOverride = 'foo';
		$this->qrcode = new QRCode;

		$this::assertInstanceOf(Number::class, $this->qrcode->initDataInterface('123'));
		$this::assertInstanceOf(AlphaNum::class, $this->qrcode->initDataInterface('ABC123'));
		$this::assertInstanceOf(Byte::class, $this->qrcode->initDataInterface(random_bytes(32)));
		$this::assertInstanceOf(Kanji::class, $this->qrcode->initDataInterface('茗荷'));

		
		$this->options->dataModeOverride = 'Byte';
		$this->qrcode = new QRCode($this->options);

		$this::assertInstanceOf(Byte::class, $this->qrcode->initDataInterface('123'));
		$this::assertInstanceOf(Byte::class, $this->qrcode->initDataInterface('ABC123'));
		$this::assertInstanceOf(Byte::class, $this->qrcode->initDataInterface(random_bytes(32)));
		$this::assertInstanceOf(Byte::class, $this->qrcode->initDataInterface('茗荷'));
	}

	
	public function testDataModeOverrideError():void{
		$this->expectException(QRCodeDataException::class);
		$this->expectExceptionMessage('illegal char:');

		$this->options->dataModeOverride = 'AlphaNum';

		(new QRCode($this->options))->initDataInterface(random_bytes(32));
	}

}
