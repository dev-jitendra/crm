<?php


namespace chillerlan\QRCode\Output;

use chillerlan\QRCode\QRCode;

use function is_string, sprintf, strip_tags, trim;


class QRMarkup extends QROutputAbstract{

	protected string $defaultMode = QRCode::OUTPUT_MARKUP_SVG;

	
	protected string $svgHeader = '<svg xmlns="http:
	                              'style="width: 100%%; height: auto;" viewBox="0 0 %2$d %2$d">';

	
	protected function setModuleValues():void{

		foreach($this::DEFAULT_MODULE_VALUES as $M_TYPE => $defaultValue){
			$v = $this->options->moduleValues[$M_TYPE] ?? null;

			if(!is_string($v)){
				$this->moduleValues[$M_TYPE] = $defaultValue
					? $this->options->markupDark
					: $this->options->markupLight;
			}
			else{
				$this->moduleValues[$M_TYPE] = trim(strip_tags($v), '\'"');
			}

		}

	}

	
	protected function html(string $file = null):string{

		$html = empty($this->options->cssClass)
			? '<div>'
			: '<div class="'.$this->options->cssClass.'">';

		$html .= $this->options->eol;

		foreach($this->matrix->matrix() as $row){
			$html .= '<div>';

			foreach($row as $M_TYPE){
				$html .= '<span style="background: '.$this->moduleValues[$M_TYPE].';"></span>';
			}

			$html .= '</div>'.$this->options->eol;
		}

		$html .= '</div>'.$this->options->eol;

		if($file !== null){
			return '<!DOCTYPE html>'.
			       '<head><meta charset="UTF-8"><title>QR Code</title></head>'.
			       '<body>'.$this->options->eol.$html.'</body>';
		}

		return $html;
	}

	
	protected function svg(string $file = null):string{
		$matrix = $this->matrix->matrix();

		$svg = sprintf($this->svgHeader, $this->options->cssClass, $this->options->svgViewBoxSize ?? $this->moduleCount)
		       .$this->options->eol
		       .'<defs>'.$this->options->svgDefs.'</defs>'
		       .$this->options->eol;

		foreach($this->moduleValues as $M_TYPE => $value){
			$path = '';

			foreach($matrix as $y => $row){
				
				$start = null;
				$count = 0;

				foreach($row as $x => $module){

					if($module === $M_TYPE){
						$count++;

						if($start === null){
							$start = $x;
						}

						if(isset($row[$x + 1])){
							continue;
						}
					}

					if($count > 0){
						$len   = $count;
						$start ??= 0; 

						$path .= sprintf('M%s %s h%s v1 h-%sZ ', $start, $y, $len, $len);

						
						$count = 0;
						$start = null;
					}

				}

			}

			if(!empty($path)){
				$svg .= sprintf(
					'<path class="qr-%s %s" stroke="transparent" fill="%s" fill-opacity="%s" d="%s" />',
					$M_TYPE, $this->options->cssClass, $value, $this->options->svgOpacity, $path
				);
			}

		}

		
		$svg .= '</svg>'.$this->options->eol;

		
		if($file !== null){
			return '<!DOCTYPE svg PUBLIC "-
			       $this->options->eol.$svg;
		}

		if($this->options->imageBase64){
			$svg = sprintf('data:image/svg+xml;base64,%s', base64_encode($svg));
		}

		return $svg;
	}

}
