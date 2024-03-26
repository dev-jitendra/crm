<?php


namespace chillerlan\QRCodeTest\Output;

use FPDF;
use chillerlan\QRCode\Output\{QRFpdf, QROutputInterface};
use chillerlan\QRCode\{QRCode, QROptions};

use function class_exists, substr;


class QRFpdfTest extends QROutputTestAbstract{

	
	public function setUp():void{

		if(!class_exists(FPDF::class)){
			$this->markTestSkipped('FPDF not available');

			
			return;
		}

		parent::setUp();
	}

	
	protected function getOutputInterface(QROptions $options):QROutputInterface{
		return new QRFpdf($options, $this->matrix);
	}

	
	public function types():array{
		return [
			'fpdf' => [QRCode::OUTPUT_FPDF],
		];
	}

	
	public function testSetModuleValues():void{

		$this->options->moduleValues = [
			
			1024 => [0, 0, 0],
			4    => [255, 255, 255],
		];

		$this->outputInterface = $this->getOutputInterface($this->options);
		$this->outputInterface->dump();

		$this::assertTrue(true); 
	}

	
	public function testRenderImage(string $type):void{
		$this->options->outputType  = $type;
		$this->options->imageBase64 = false;

		
		$expected = substr(file_get_contents(__DIR__.'/samples/'.$type), 0, 2500);
		$actual   = substr((new QRCode($this->options))->render('test'), 0, 2500);

		$this::assertSame($expected, $actual);
	}

	public function testOutputGetResource():void{
		$this->options->returnResource = true;
		$this->outputInterface         = $this->getOutputInterface($this->options);

		$this::assertInstanceOf(FPDF::class, $this->outputInterface->dump());
	}

}
