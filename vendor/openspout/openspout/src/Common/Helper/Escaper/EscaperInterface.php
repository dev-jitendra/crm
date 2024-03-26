<?php

declare(strict_types=1);

namespace OpenSpout\Common\Helper\Escaper;


interface EscaperInterface
{
    
    public function escape(string $string): string;

    
    public function unescape(string $string): string;
}
