<?php


namespace chillerlan\QRCodeExamples;

use chillerlan\QRCode\{QRCode, QROptions};

require_once __DIR__.'/../vendor/autoload.php';

$data = 'https:

$options = new QROptions([
	'version'      => 5,
	'outputType'   => QRCode::OUTPUT_STRING_TEXT,
	'eccLevel'     => QRCode::ECC_L,
]);


echo '<pre style="font-size: 75%; line-height: 1;">'.(new QRCode($options))->render($data).'</pre>';



$options = new QROptions([
	'version'      => 5,
	'outputType'   => QRCode::OUTPUT_STRING_TEXT,
	'eccLevel'     => QRCode::ECC_L,
	'moduleValues' => [
		
		1536 => 'A', 
		6    => 'a', 
		
		2560 => 'B',
		10   => 'b',
		
		3072 => 'C',
		12   => 'c',
		
		3584 => 'D',
		14   => 'd',
		
		4096 => 'E',
		16   => 'e',
		
		1024 => 'F',
		4    => 'f',
		
		512  => 'G',
		
		8    => 'h',
		
		18   => 'i',
	],
]);


echo '<pre style="font-size: 75%; line-height: 1;">'.(new QRCode($options))->render($data).'</pre>';





