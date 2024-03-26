<?php


namespace chillerlan\QRCodeExamples;

use chillerlan\QRCode\{QRCode, QROptions};

require_once '../vendor/autoload.php';

header('Content-Type: text/html; charset=utf-8');

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<title>QRCode test</title>
	<style>
		body{
			margin: 5em;
			padding: 0;
		}

		div.qrcode{
			margin: 0;
			padding: 0;
		}

		
		div.qrcode > div {
			margin: 0;
			padding: 0;
			height: 10px;
		}

		
		div.qrcode > div > span {
			display: inline-block;
			width: 10px;
			height: 10px;
		}

		div.qrcode > div > span {
			background-color: #ccc;
		}
	</style>
</head>
<body>
	<div class="qrcode">
<?php

	$data = 'https:

	$options = new QROptions([
		'version' => 5,
		'outputType' => QRCode::OUTPUT_MARKUP_HTML,
		'eccLevel' => QRCode::ECC_L,
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
			
			8    => '#AFBFBF',
			
			18   => '#FFFFFF',
		],
	]);

	echo (new QRCode($options))->render($data);

?>
	</div>
</body>
</html>



