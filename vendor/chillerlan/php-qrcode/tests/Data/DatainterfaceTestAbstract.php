<?php


namespace chillerlan\QRCodeTest\Data;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use PHPUnit\Framework\TestCase;
use chillerlan\QRCode\Data\{QRCodeDataException, QRDataInterface, QRMatrix};
use ReflectionClass;

use function str_repeat;


abstract class DatainterfaceTestAbstract extends TestCase{

	
	protected ReflectionClass $reflection;
	
	protected QRDataInterface $dataInterface;
	
	protected string $testdata;
	
	protected array  $expected;

	
	protected function setUp():void{
		$this->dataInterface = $this->getDataInterfaceInstance(new QROptions(['version' => 4]));
		$this->reflection    = new ReflectionClass($this->dataInterface);
	}

	
	abstract protected function getDataInterfaceInstance(QROptions $options):QRDataInterface;

	
	public function testInstance():void{
		$this::assertInstanceOf(QRDataInterface::class, $this->dataInterface);
	}

	
	public function testMaskEcc():void{
		$this->dataInterface->setData($this->testdata);

		$maskECC = $this->reflection->getMethod('maskECC');
		$maskECC->setAccessible(true);

		$this::assertSame($this->expected, $maskECC->invoke($this->dataInterface));
	}

	
	public function MaskPatternProvider():array{
		return [[0], [1], [2], [3], [4], [5], [6], [7]];
	}

	
	public function testInitMatrix(int $maskPattern):void{
		$this->dataInterface->setData($this->testdata);

		$matrix = $this->dataInterface->initMatrix($maskPattern);

		$this::assertInstanceOf(QRMatrix::class, $matrix);
		$this::assertSame($maskPattern, $matrix->maskPattern());
	}

	
	public function testGetMinimumVersion():void{
		$this->dataInterface->setData($this->testdata);

		$getMinimumVersion = $this->reflection->getMethod('getMinimumVersion');
		$getMinimumVersion->setAccessible(true);

		$this::assertSame(1, $getMinimumVersion->invoke($this->dataInterface));
	}

	
	public function testGetMinimumVersionException():void{
		$this->expectException(QRCodeDataException::class);
		$this->expectExceptionMessage('data exceeds');

		$this->dataInterface = $this->getDataInterfaceInstance(new QROptions(['version' => QRCode::VERSION_AUTO]));
		$this->dataInterface->setData(str_repeat($this->testdata, 1337));
	}

	
	public function testCodeLengthOverflowException():void{
		$this->expectException(QRCodeDataException::class);
		$this->expectExceptionMessage('code length overflow');

		$this->dataInterface->setData(str_repeat($this->testdata, 1337));
	}

}
