<?php

namespace PhpOffice\PhpSpreadsheet\RichText;

interface ITextElement
{
    
    public function getText();

    
    public function setText($text);

    
    public function getFont();

    
    public function getHashCode();
}
