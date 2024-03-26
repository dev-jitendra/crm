<?php

declare(strict_types=1);

namespace OpenSpout\Common\Helper\Escaper;


final class ODS implements EscaperInterface
{
    
    public function escape(string $string): string
    {
        
        return htmlspecialchars($string, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8');
    }

    
    public function unescape(string $string): string
    {
        
        
        
        
        
        
        return $string;
    }
}
