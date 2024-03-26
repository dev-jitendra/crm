<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;


class HeaderFooter
{
    
    const IMAGE_HEADER_LEFT = 'LH';
    const IMAGE_HEADER_CENTER = 'CH';
    const IMAGE_HEADER_RIGHT = 'RH';
    const IMAGE_FOOTER_LEFT = 'LF';
    const IMAGE_FOOTER_CENTER = 'CF';
    const IMAGE_FOOTER_RIGHT = 'RF';

    
    private $oddHeader = '';

    
    private $oddFooter = '';

    
    private $evenHeader = '';

    
    private $evenFooter = '';

    
    private $firstHeader = '';

    
    private $firstFooter = '';

    
    private $differentOddEven = false;

    
    private $differentFirst = false;

    
    private $scaleWithDocument = true;

    
    private $alignWithMargins = true;

    
    private $headerFooterImages = [];

    
    public function __construct()
    {
    }

    
    public function getOddHeader()
    {
        return $this->oddHeader;
    }

    
    public function setOddHeader($pValue)
    {
        $this->oddHeader = $pValue;

        return $this;
    }

    
    public function getOddFooter()
    {
        return $this->oddFooter;
    }

    
    public function setOddFooter($pValue)
    {
        $this->oddFooter = $pValue;

        return $this;
    }

    
    public function getEvenHeader()
    {
        return $this->evenHeader;
    }

    
    public function setEvenHeader($pValue)
    {
        $this->evenHeader = $pValue;

        return $this;
    }

    
    public function getEvenFooter()
    {
        return $this->evenFooter;
    }

    
    public function setEvenFooter($pValue)
    {
        $this->evenFooter = $pValue;

        return $this;
    }

    
    public function getFirstHeader()
    {
        return $this->firstHeader;
    }

    
    public function setFirstHeader($pValue)
    {
        $this->firstHeader = $pValue;

        return $this;
    }

    
    public function getFirstFooter()
    {
        return $this->firstFooter;
    }

    
    public function setFirstFooter($pValue)
    {
        $this->firstFooter = $pValue;

        return $this;
    }

    
    public function getDifferentOddEven()
    {
        return $this->differentOddEven;
    }

    
    public function setDifferentOddEven($pValue)
    {
        $this->differentOddEven = $pValue;

        return $this;
    }

    
    public function getDifferentFirst()
    {
        return $this->differentFirst;
    }

    
    public function setDifferentFirst($pValue)
    {
        $this->differentFirst = $pValue;

        return $this;
    }

    
    public function getScaleWithDocument()
    {
        return $this->scaleWithDocument;
    }

    
    public function setScaleWithDocument($pValue)
    {
        $this->scaleWithDocument = $pValue;

        return $this;
    }

    
    public function getAlignWithMargins()
    {
        return $this->alignWithMargins;
    }

    
    public function setAlignWithMargins($pValue)
    {
        $this->alignWithMargins = $pValue;

        return $this;
    }

    
    public function addImage(HeaderFooterDrawing $image, $location = self::IMAGE_HEADER_LEFT)
    {
        $this->headerFooterImages[$location] = $image;

        return $this;
    }

    
    public function removeImage($location = self::IMAGE_HEADER_LEFT)
    {
        if (isset($this->headerFooterImages[$location])) {
            unset($this->headerFooterImages[$location]);
        }

        return $this;
    }

    
    public function setImages(array $images)
    {
        $this->headerFooterImages = $images;

        return $this;
    }

    
    public function getImages()
    {
        
        $images = [];
        if (isset($this->headerFooterImages[self::IMAGE_HEADER_LEFT])) {
            $images[self::IMAGE_HEADER_LEFT] = $this->headerFooterImages[self::IMAGE_HEADER_LEFT];
        }
        if (isset($this->headerFooterImages[self::IMAGE_HEADER_CENTER])) {
            $images[self::IMAGE_HEADER_CENTER] = $this->headerFooterImages[self::IMAGE_HEADER_CENTER];
        }
        if (isset($this->headerFooterImages[self::IMAGE_HEADER_RIGHT])) {
            $images[self::IMAGE_HEADER_RIGHT] = $this->headerFooterImages[self::IMAGE_HEADER_RIGHT];
        }
        if (isset($this->headerFooterImages[self::IMAGE_FOOTER_LEFT])) {
            $images[self::IMAGE_FOOTER_LEFT] = $this->headerFooterImages[self::IMAGE_FOOTER_LEFT];
        }
        if (isset($this->headerFooterImages[self::IMAGE_FOOTER_CENTER])) {
            $images[self::IMAGE_FOOTER_CENTER] = $this->headerFooterImages[self::IMAGE_FOOTER_CENTER];
        }
        if (isset($this->headerFooterImages[self::IMAGE_FOOTER_RIGHT])) {
            $images[self::IMAGE_FOOTER_RIGHT] = $this->headerFooterImages[self::IMAGE_FOOTER_RIGHT];
        }
        $this->headerFooterImages = $images;

        return $this->headerFooterImages;
    }

    
    public function __clone()
    {
        $vars = get_object_vars($this);
        foreach ($vars as $key => $value) {
            if (is_object($value)) {
                $this->$key = clone $value;
            } else {
                $this->$key = $value;
            }
        }
    }
}
