<?php


namespace chillerlan\QRCodeExamples;

use chillerlan\QRCode\{QRCode, QROptions};

require_once __DIR__.'/../vendor/autoload.php';

$data = 'https:


$options = new QROptions([
	'version'      => 5,
	'eccLevel'     => QRCode::ECC_L,
]);

$qrOutputInterface = new MyCustomOutput($options, (new QRCode($options))->getMatrix($data));

var_dump($qrOutputInterface->dump());



$options = new QROptions([
	'version'         => 5,
	'eccLevel'        => QRCode::ECC_L,
	'outputType'      => QRCode::OUTPUT_CUSTOM,
	'outputInterface' => MyCustomOutput::class,
]);

var_dump((new QRCode($options))->render($data));
