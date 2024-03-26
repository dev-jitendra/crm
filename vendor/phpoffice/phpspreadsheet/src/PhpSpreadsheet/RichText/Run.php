<?php

namespace PhpOffice\PhpSpreadsheet\RichText;

use PhpOffice\PhpSpreadsheet\Style\Font;

class Run extends TextElement implements ITextElement
{
    
    private $font;

    
    public function __construct($pText = '')
    {
        parent::__construct($pText);
        
        $this->font = new Font();
    }

    
    public function getFont()
    {
        return $this->font;
    }

    
    public function setFont(?Font $pFont = null)
    {
        $this->font = $pFont;

        return $this;
    }

    
    public function getHashCode()
    {
        return md5(
            $this->getText() .
            $this->font->getHashCode() .
            __CLASS__
        );
    }
}
