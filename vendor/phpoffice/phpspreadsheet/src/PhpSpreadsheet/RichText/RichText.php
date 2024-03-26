<?php

namespace PhpOffice\PhpSpreadsheet\RichText;

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IComparable;

class RichText implements IComparable
{
    
    private $richTextElements;

    
    public function __construct(?Cell $pCell = null)
    {
        
        $this->richTextElements = [];

        
        if ($pCell !== null) {
            
            if ($pCell->getValue() != '') {
                $objRun = new Run($pCell->getValue());
                $objRun->setFont(clone $pCell->getWorksheet()->getStyle($pCell->getCoordinate())->getFont());
                $this->addText($objRun);
            }

            
            $pCell->setValueExplicit($this, DataType::TYPE_STRING);
        }
    }

    
    public function addText(ITextElement $pText)
    {
        $this->richTextElements[] = $pText;

        return $this;
    }

    
    public function createText($pText)
    {
        $objText = new TextElement($pText);
        $this->addText($objText);

        return $objText;
    }

    
    public function createTextRun($pText)
    {
        $objText = new Run($pText);
        $this->addText($objText);

        return $objText;
    }

    
    public function getPlainText()
    {
        
        $returnValue = '';

        
        foreach ($this->richTextElements as $text) {
            $returnValue .= $text->getText();
        }

        return $returnValue;
    }

    
    public function __toString()
    {
        return $this->getPlainText();
    }

    
    public function getRichTextElements()
    {
        return $this->richTextElements;
    }

    
    public function setRichTextElements(array $textElements)
    {
        $this->richTextElements = $textElements;

        return $this;
    }

    
    public function getHashCode()
    {
        $hashElements = '';
        foreach ($this->richTextElements as $element) {
            $hashElements .= $element->getHashCode();
        }

        return md5(
            $hashElements .
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
