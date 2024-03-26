<?php

namespace Identicon\Generator;


interface GeneratorInterface
{
    
    public function getImageBinaryData($string, $size = null, $color = null, $backgroundColor = null);

    
    public function getImageResource($string, $size = null, $color = null, $backgroundColor = null);

    
    public function getMimeType();
	
	
	public function getColor();
}
