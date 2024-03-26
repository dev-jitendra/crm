<?php

namespace PhpOffice\PhpSpreadsheet\RichText;

class TextElement implements ITextElement
{
    
    private $text;

    
    public function __construct($pText = '')
    {
        
        $this->text = $pText;
    }

    
    public function getText()
    {
        return $this->text;
    }

    
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    
    public function getFont()
    {
        return null;
    }

    
    public function getHashCode()
    {
        return md5(
            $this->text .
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
}
