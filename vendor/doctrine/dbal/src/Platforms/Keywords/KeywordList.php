<?php

namespace Doctrine\DBAL\Platforms\Keywords;

use function array_flip;
use function array_map;
use function strtoupper;


abstract class KeywordList
{
    
    private ?array $keywords = null;

    
    public function isKeyword($word)
    {
        if ($this->keywords === null) {
            $this->initializeKeywords();
        }

        return isset($this->keywords[strtoupper($word)]);
    }

    
    protected function initializeKeywords()
    {
        $this->keywords = array_flip(array_map('strtoupper', $this->getKeywords()));
    }

    
    abstract protected function getKeywords();

    
    abstract public function getName();
}
