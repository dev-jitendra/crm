<?php


namespace chillerlan\QRCodeTest\Output;

use chillerlan\QRCode\{QRCode, QROptions};
use chillerlan\QRCode\Output\{QROutputInterface, QRImage};


class QRImageTest extends QROutputTestAbstract{

	
	public function setUp():void{

		if(!extension_loaded('gd')){
			$this->markTestSkipped('ext-gd not loaded');
			return;
		}

		parent::setUp();
	}

	
	protected function getOutputInterface(QROptions $options):QROutputInterface{
		return new QRImage($options, $this->matrix);
	}

	
	public function types():array{
		return [
			'png' => [QRCode::OUTPUT_IMAGE_PNG],
			'gif' => [QRCode::OUTPUT_IMAGE_GIF],
			'jpg' => [QRCode::OUTPUT_IMAGE_JPG],
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

	
	public function testOutputGetResource():void{
		$this->options->returnResource = true;
		$this->outputInterface         = $this->getOutputInterface($this->options);

		$actual = $this->outputInterface->dump();

		
		\PHP_MAJOR_VERSION >= 8
			? $this::assertInstanceOf(\GdImage::class, $actual)
			: $this::assertIsResource($actual);
	}

}
