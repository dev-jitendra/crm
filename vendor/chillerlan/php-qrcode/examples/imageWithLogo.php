<?php


namespace chillerlan\QRCodeExamples;

use chillerlan\QRCode\{QRCode, QROptions};

require_once __DIR__.'/../vendor/autoload.php';

$data = 'https:

class LogoOptions extends QROptions{
	
	protected int $logoSpaceWidth;
	protected int $logoSpaceHeight;
}

$options = new LogoOptions;

$options->version          = 7;
$options->eccLevel         = QRCode::ECC_H;
$options->imageBase64      = false;
$options->logoSpaceWidth   = 13;
$options->logoSpaceHeight  = 13;
$options->scale            = 5;
$options->imageTransparent = false;

header('Content-type: image/png');

$qrOutputInterface = new QRImageWithLogo($options, (new QRCode($options))->getMatrix($data));


echo $qrOutputInterface->dump(null, __DIR__.'/octocat.png');
