<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

use GdImage;

class MemoryDrawing extends BaseDrawing
{
    
    const RENDERING_DEFAULT = 'imagepng';
    const RENDERING_PNG = 'imagepng';
    const RENDERING_GIF = 'imagegif';
    const RENDERING_JPEG = 'imagejpeg';

    
    const MIMETYPE_DEFAULT = 'image/png';
    const MIMETYPE_PNG = 'image/png';
    const MIMETYPE_GIF = 'image/gif';
    const MIMETYPE_JPEG = 'image/jpeg';

    
    private $imageResource;

    
    private $renderingFunction;

    
    private $mimeType;

    
    private $uniqueName;

    
    public function __construct()
    {
        
        $this->imageResource = null;
        $this->renderingFunction = self::RENDERING_DEFAULT;
        $this->mimeType = self::MIMETYPE_DEFAULT;
        $this->uniqueName = md5(mt_rand(0, 9999) . time() . mt_rand(0, 9999));

        
        parent::__construct();
    }

    
    public function getImageResource()
    {
        return $this->imageResource;
    }

    
    public function setImageResource($value)
    {
        $this->imageResource = $value;

        if ($this->imageResource !== null) {
            
            $this->width = imagesx($this->imageResource);
            $this->height = imagesy($this->imageResource);
        }

        return $this;
    }

    
    public function getRenderingFunction()
    {
        return $this->renderingFunction;
    }

    
    public function setRenderingFunction($value)
    {
        $this->renderingFunction = $value;

        return $this;
    }

    
    public function getMimeType()
    {
        return $this->mimeType;
    }

    
    public function setMimeType($value)
    {
        $this->mimeType = $value;

        return $this;
    }

    
    public function getIndexedFilename()
    {
        $extension = strtolower($this->getMimeType());
        $extension = explode('/', $extension);
        $extension = $extension[1];

        return $this->uniqueName . $this->getImageIndex() . '.' . $extension;
    }

    
    public function getHashCode()
    {
        return md5(
            $this->renderingFunction .
            $this->mimeType .
            $this->uniqueName .
            parent::getHashCode() .
            __CLASS__
        );
    }
}
