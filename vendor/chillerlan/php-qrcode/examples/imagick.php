<?php


namespace chillerlan\QRCodeExamples;

use chillerlan\QRCode\{QRCode, QROptions};

require_once __DIR__.'/../vendor/autoload.php';

$data = 'https:

$options = new QROptions([
	'version'      => 7,
	'outputType'   => QRCode::OUTPUT_IMAGICK,
	'eccLevel'     => QRCode::ECC_L,
	'scale'        => 5,
	'moduleValues' => [
		
		1536 => '#A71111', 
		6    => '#FFBFBF', 
		
		2560 => '#A70364',
		10   => '#FFC9C9',
		
		3072 => '#98005D',
		12   => '#FFB8E9',
		
		3584 => '#003804',
		14   => '#00FB12',
		
		4096 => '#650098',
		16   => '#E0B8FF',
		
		1024 => '#4A6000',
		4    => '#ECF9BE',
		
		512  => '#080063',
		
		8    => '#DDDDDD',
		
		18   => '#DDDDDD',
	],
]);

header('Content-type: image/png');

echo (new QRCode($options))->render($data);





