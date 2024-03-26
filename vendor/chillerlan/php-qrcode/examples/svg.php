<?php


namespace chillerlan\QRCodeExamples;

use chillerlan\QRCode\{QRCode, QROptions};

require_once __DIR__.'/../vendor/autoload.php';

$data = 'https:
$gzip = true;

$options = new QROptions([
	'version'      => 7,
	'outputType'   => QRCode::OUTPUT_MARKUP_SVG,
	'imageBase64'  => false,
	'eccLevel'     => QRCode::ECC_L,
	'svgViewBoxSize' => 530,
	'addQuietzone' => true,
	'cssClass'     => 'my-css-class',
	'svgOpacity'   => 1.0,
	'svgDefs'      => '
		<linearGradient id="g2">
			<stop offset="0%" stop-color="#39F" />
			<stop offset="100%" stop-color="#F3F" />
		</linearGradient>
		<linearGradient id="g1">
			<stop offset="0%" stop-color="#F3F" />
			<stop offset="100%" stop-color="#39F" />
		</linearGradient>
		<style>rect{shape-rendering:crispEdges}</style>',
	'moduleValues' => [
		
		1536 => 'url(#g1)', 
		6    => '#fff', 
		
		2560 => 'url(#g1)',
		10   => '#fff',
		
		3072 => 'url(#g1)',
		12   => '#fff',
		
		3584 => 'url(#g1)',
		14   => '#fff',
		
		4096 => 'url(#g1)',
		16   => '#fff',
		
		1024 => 'url(#g2)',
		4    => '#fff',
		
		512  => 'url(#g1)',
		
		8    => '#fff',
		
		18   => '#fff',
	],
]);

$qrcode = (new QRCode($options))->render($data);

header('Content-type: image/svg+xml');

if($gzip === true){
	header('Vary: Accept-Encoding');
	header('Content-Encoding: gzip');
	$qrcode = gzencode($qrcode ,9);
}
echo $qrcode;


