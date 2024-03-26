<?php


namespace chillerlan\QRCodeExamples;

use chillerlan\QRCode\{QRCode, QROptions};

require_once __DIR__.'/../vendor/autoload.php';

$data = 'https:

$options = new QROptions([
	'version'      => 7,
	'outputType'   => QRCode::OUTPUT_IMAGE_PNG,
	'scale'        => 3,
	'imageBase64'  => false,
]);

header('Content-type: image/png');

$qrOutputInterface = new QRImageWithText($options, (new QRCode($options))->getMatrix($data));


echo $qrOutputInterface->dump(null, 'example text');
