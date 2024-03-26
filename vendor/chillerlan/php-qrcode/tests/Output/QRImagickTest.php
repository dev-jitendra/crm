<?php


namespace chillerlan\QRCodeTest\Output;

use Imagick;
use chillerlan\QRCode\{QRCode, QROptions};
use chillerlan\QRCode\Output\{QROutputInterface, QRImagick};


class QRImagickTest extends QROutputTestAbstract{

	
	public function setUp():void{

		if(!extension_loaded('imagick')){
			$this->markTestSkipped('ext-imagick not loaded');

			
			return;
		}

		parent::setUp();
	}

	
	protected function getOutputInterface(QROptions $options):QROutputInterface{
		return new QRImagick($options, $this->matrix);
	}

	
	public function types():array{
		return [
			'imagick' => [QRCode::OUTPUT_IMAGICK],
		];
	}

	
	public function testSetModuleValues():void{

		$this->options->moduleValues = [
			
			1024 => '#4A6000',
			4    => '#ECF9BE',
		];

		$this->outputInterface = $this->getOutputInterface($this->options);
		$this->outputInterface->dump();

		$this::assertTrue(true); 
	}

	public function testOutputGetResource():void{
		$this->options->returnResource = true;
		$this->outputInterface         = $this->getOutputInterface($this->options);

		$this::assertInstanceOf(Imagick::class, $this->outputInterface->dump());
	}


}
