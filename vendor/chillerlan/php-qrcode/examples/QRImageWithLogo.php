<?php


namespace chillerlan\QRCodeExamples;

use chillerlan\QRCode\Output\{QRCodeOutputException, QRImage};

use function imagecopyresampled, imagecreatefrompng, imagesx, imagesy, is_file, is_readable;


class QRImageWithLogo extends QRImage{

	
	public function dump(string $file = null, string $logo = null):string{
		
		$this->options->returnResource = true;

		
		
		if(!is_file($logo) || !is_readable($logo)){
			throw new QRCodeOutputException('invalid logo');
		}

		$this->matrix->setLogoSpace(
			$this->options->logoSpaceWidth,
			$this->options->logoSpaceHeight
			
		);

		
		parent::dump($file);

		$im = imagecreatefrompng($logo);

		
		$w = imagesx($im);
		$h = imagesy($im);

		
		$lw = ($this->options->logoSpaceWidth - 2) * $this->options->scale;
		$lh = ($this->options->logoSpaceHeight - 2) * $this->options->scale;

		
		$ql = $this->matrix->size() * $this->options->scale;

		
		imagecopyresampled($this->image, $im, ($ql - $lw) / 2, ($ql - $lh) / 2, 0, 0, $lw, $lh, $w, $h);

		$imageData = $this->dumpImage();

		if($file !== null){
			$this->saveToFile($imageData, $file);
		}

		if($this->options->imageBase64){
			$imageData = 'data:image/'.$this->options->outputType.';base64,'.base64_encode($imageData);
		}

		return $imageData;
	}

}
