<?php


namespace chillerlan\QRCodeExamples;

use chillerlan\QRCode\Output\QRImage;

use function base64_encode, imagechar, imagecolorallocate, imagecolortransparent, imagecopymerge, imagecreatetruecolor,
	imagedestroy, imagefilledrectangle, imagefontwidth, in_array, round, str_split, strlen;

class QRImageWithText extends QRImage{

	
	public function dump(string $file = null, string $text = null):string{
		
		$this->options->returnResource = true;

		
		parent::dump($file);

		
		if($text !== null){
			$this->addText($text);
		}

		$imageData = $this->dumpImage();

		if($file !== null){
			$this->saveToFile($imageData, $file);
		}

		if($this->options->imageBase64){
			$imageData = 'data:image/'.$this->options->outputType.';base64,'.base64_encode($imageData);
		}

		return $imageData;
	}

	
	protected function addText(string $text):void{
		
		$qrcode = $this->image;

		
		$textSize  = 3; 
		$textBG    = [200, 200, 200];
		$textColor = [50, 50, 50];

		$bgWidth  = $this->length;
		$bgHeight = $bgWidth + 20; 

		
		$this->image = imagecreatetruecolor($bgWidth, $bgHeight);
		$background  = imagecolorallocate($this->image, ...$textBG);

		
		if($this->options->imageTransparent && in_array($this->options->outputType, $this::TRANSPARENCY_TYPES, true)){
			imagecolortransparent($this->image, $background);
		}

		
		imagefilledrectangle($this->image, 0, 0, $bgWidth, $bgHeight, $background);

		
		imagecopymerge($this->image, $qrcode, 0, 0, 0, 0, $this->length, $this->length, 100);
		imagedestroy($qrcode);

		$fontColor = imagecolorallocate($this->image, ...$textColor);
		$w         = imagefontwidth($textSize);
		$x         = round(($bgWidth - strlen($text) * $w) / 2);

		
		foreach(str_split($text) as $i => $chr){
			imagechar($this->image, $textSize, (int)($i * $w + $x), $this->length, $chr, $fontColor);
		}
	}

}
