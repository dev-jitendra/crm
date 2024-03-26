<?php

declare(strict_types=1);

namespace OpenSpout\Common\Entity\Comment;


final class Comment
{
    
    public string $height = '55.5pt';

    
    public string $width = '96pt';

    
    public string $marginLeft = '59.25pt';

    
    public string $marginTop = '1.5pt';

    
    public bool $visible = false;

    
    public string $fillColor = '#FFFFE1';

    
    private array $textRuns = [];

    public function addTextRun(?TextRun $textRun): void
    {
        $this->textRuns[] = $textRun;
    }

    
    public function getTextRuns(): array
    {
        return $this->textRuns;
    }
}
