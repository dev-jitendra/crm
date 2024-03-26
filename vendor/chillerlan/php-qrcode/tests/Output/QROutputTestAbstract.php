<?php


namespace chillerlan\QRCodeTest\Output;

use chillerlan\QRCode\{QRCode, QROptions};
use chillerlan\QRCode\Data\{Byte, QRMatrix};
use chillerlan\QRCode\Output\{QRCodeOutputException, QROutputInterface};
use PHPUnit\Framework\TestCase;

use function file_exists, in_array, mkdir;

use const PHP_OS_FAMILY, PHP_VERSION_ID;


abstract class QROutputTestAbstract extends TestCase{

	
	protected string $builddir = __DIR__.'/../../.build/output_test';
	
	protected QROutputInterface $outputInterface;
	
	protected QROptions $options;
	
	protected QRMatrix $matrix;

	
	protected function setUp():void{

		if(!file_exists($this->builddir)){
			mkdir($this->builddir, 0777, true);
		}

		$this->options         = new QROptions;
		$this->matrix          = (new Byte($this->options, 'testdata'))->initMatrix(0);
		$this->outputInterface = $this->getOutputInterface($this->options);
	}

	
	abstract protected function getOutputInterface(QROptions $options):QROutputInterface;

	
	public function testInstance():void{
		$this::assertInstanceOf(QROutputInterface::class, $this->outputInterface);
	}

	
	public function testSaveException():void{
		$this->expectException(QRCodeOutputException::class);
		$this->expectExceptionMessage('Could not write data to cache file: /foo/bar.test');

		$this->options->cachefile = '/foo/bar.test';
		$this->outputInterface = $this->getOutputInterface($this->options);
		$this->outputInterface->dump();
	}

	
	abstract public function testSetModuleValues():void;

	

	
	abstract public function types():array;

	
	public function testStringOutput(string $type):void{
		$this->options->outputType  = $type;
		$this->options->cachefile   = $this->builddir.'/test.'.$type;
		$this->options->imageBase64 = false;

		$this->outputInterface     = $this->getOutputInterface($this->options);
		$data                      = $this->outputInterface->dump(); 

		$this::assertSame($data, file_get_contents($this->options->cachefile));
	}

	
	public function testRenderImage(string $type):void{

		
		
		if(
			(PHP_OS_FAMILY !== 'Windows' || PHP_VERSION_ID >= 80100)
			&& in_array($type, [QRCode::OUTPUT_IMAGE_JPG, QRCode::OUTPUT_IMAGICK, QRCode::OUTPUT_MARKUP_SVG])
		){
			$this::markTestSkipped('may fail on CI');

			
			return;
		}

		$this->options->outputType = $type;

		$this::assertSame(
			trim(file_get_contents(__DIR__.'/samples/'.$type)),
			trim((new QRCode($this->options))->render('test'))
		);
	}

}
