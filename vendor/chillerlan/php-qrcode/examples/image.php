<?php


namespace chillerlan\QRCodeExamples;

use chillerlan\QRCode\{QRCode, QROptions};

require_once __DIR__.'/../vendor/autoload.php';

$data = 'https:

$options = new QROptions([
	'version'      => 10,
	'outputType'   => QRCode::OUTPUT_IMAGE_PNG,
	'eccLevel'     => QRCode::ECC_H,
	'scale'        => 5,
	'imageBase64'  => false,
	'moduleValues' => [
		
		1536 => [0, 63, 255], 
		6    => [255, 255, 255], 
		5632 => [241, 28, 163], 
		
		2560 => [255, 0, 255],
		10   => [255, 255, 255],
		
		3072 => [255, 0, 0],
		12   => [255, 255, 255],
		
		3584 => [67, 99, 84],
		14   => [255, 255, 255],
		
		4096 => [62, 174, 190],
		16   => [255, 255, 255],
		
		1024 => [0, 0, 0],
		4    => [255, 255, 255],
		
		512  => [0, 0, 0],
		
		8    => [255, 255, 255],
		
		18   => [255, 255, 255],
		
		20    => [255, 255, 255],
	],
]);

header('Content-type: image/png');

echo (new QRCode($options))->render($data);





