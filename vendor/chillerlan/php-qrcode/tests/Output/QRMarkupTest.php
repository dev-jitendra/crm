<?php


namespace chillerlan\QRCodeTest\Output;

use chillerlan\QRCode\{QRCode, QROptions};
use chillerlan\QRCode\Output\{QROutputInterface, QRMarkup};


class QRMarkupTest extends QROutputTestAbstract{

	
	protected function getOutputInterface(QROptions $options):QROutputInterface{
		return new QRMarkup($options, $this->matrix);
	}

	
	public function types():array{
		return [
			'html' => [QRCode::OUTPUT_MARKUP_HTML],
			'svg'  => [QRCode::OUTPUT_MARKUP_SVG],
		];
	}

	
	public function testSetModuleValues():void{
		$this->options->imageBase64  = false;
		$this->options->moduleValues = [
			
			1024 => '#4A6000',
			4    => '#ECF9BE',
		];

		$this->outputInterface = $this->getOutputInterface($this->options);
		$data = $this->outputInterface->dump();
		$this::assertStringContainsString('#4A6000', $data);
		$this::assertStringContainsString('#ECF9BE', $data);
	}

}
