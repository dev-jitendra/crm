<?php

namespace PhpOffice\PhpSpreadsheet;

use PhpOffice\PhpSpreadsheet\RichText\RichText;

class Comment implements IComparable
{
    
    private $author;

    
    private $text;

    
    private $width = '96pt';

    
    private $marginLeft = '59.25pt';

    
    private $marginTop = '1.5pt';

    
    private $visible = false;

    
    private $height = '55.5pt';

    
    private $fillColor;

    
    private $alignment;

    
    public function __construct()
    {
        
        $this->author = 'Author';
        $this->text = new RichText();
        $this->fillColor = new Style\Color('FFFFFFE1');
        $this->alignment = Style\Alignment::HORIZONTAL_GENERAL;
    }

    
    public function getAuthor()
    {
        return $this->author;
    }

    
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    
    public function getText()
    {
        return $this->text;
    }

    
    public function setText(RichText $pValue)
    {
        $this->text = $pValue;

        return $this;
    }

    
    public function getWidth()
    {
        return $this->width;
    }

    
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    
    public function getHeight()
    {
        return $this->height;
    }

    
    public function setHeight($value)
    {
        $this->height = $value;

        return $this;
    }

    
    public function getMarginLeft()
    {
        return $this->marginLeft;
    }

    
    public function setMarginLeft($value)
    {
        $this->marginLeft = $value;

        return $this;
    }

    
    public function getMarginTop()
    {
        return $this->marginTop;
    }

    
    public function setMarginTop($value)
    {
        $this->marginTop = $value;

        return $this;
    }

    
    public function getVisible()
    {
        return $this->visible;
    }

    
    public function setVisible($value)
    {
        $this->visible = $value;

        return $this;
    }

    
    public function getFillColor()
    {
        return $this->fillColor;
    }

    
    public function setAlignment($alignment)
    {
        $this->alignment = $alignment;

        return $this;
    }

    
    public function getAlignment()
    {
        return $this->alignment;
    }

    
    public function getHashCode()
    {
        return md5(
            $this->author .
            $this->text->getHashCode() .
            $this->width .
            $this->height .
            $this->marginLeft .
            $this->marginTop .
            ($this->visible ? 1 : 0) .
            $this->fillColor->getHashCode() .
            $this->alignment .
            __CLASS__
        );
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

    
    public function __toString()
    {
        return $this->text->getPlainText();
    }
}
