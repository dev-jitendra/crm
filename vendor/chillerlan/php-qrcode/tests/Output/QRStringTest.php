<?php


namespace chillerlan\QRCodeTest\Output;

use chillerlan\QRCodeExamples\MyCustomOutput;
use chillerlan\QRCode\{QRCode, QROptions};
use chillerlan\QRCode\Output\{QROutputInterface, QRString};


class QRStringTest extends QROutputTestAbstract{

	
	protected function getOutputInterface(QROptions $options):QROutputInterface{
		return new QRString($options, $this->matrix);
	}

	
	public function types():array{
		return [
			'json' => [QRCode::OUTPUT_STRING_JSON],
			'text' => [QRCode::OUTPUT_STRING_TEXT],
		];
	}

	
	public function testSetModuleValues():void{

		$this->options->moduleValues = [
			
			1024 => 'A',
			4    => 'B',
		];

		$this->outputInterface = $this->getOutputInterface($this->options);
		$data                  = $this->outputInterface->dump();

		$this::assertStringContainsString('A', $data);
		$this::assertStringContainsString('B', $data);
	}

	
	public function testCustomOutput():void{
		$this->options->version         = 5;
		$this->options->eccLevel        = QRCode::ECC_L;
		$this->options->outputType      = QRCode::OUTPUT_CUSTOM;
		$this->options->outputInterface = MyCustomOutput::class;

		$this::assertSame(
			file_get_contents(__DIR__.'/samples/custom'),
			(new QRCode($this->options))->render('test')
		);
	}

}
