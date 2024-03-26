<?php

declare(strict_types=1);

namespace OpenSpout\Common\Entity\Comment;


final class TextRun
{
    public string $text;
    public int $fontSize = 10;
    public string $fontColor = '000000';
    public string $fontName = 'Tahoma';
    public bool $bold = false;
    public bool $italic = false;

    public function __construct(string $text)
    {
        $this->text = $text;
    }
}
