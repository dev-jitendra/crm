<?php

declare(strict_types=1);

namespace OpenSpout\Common\Helper\Escaper;


final class XLSX implements EscaperInterface
{
    
    private bool $isAlreadyInitialized = false;

    
    private string $escapableControlCharactersPattern;

    
    private array $controlCharactersEscapingMap;

    
    private array $controlCharactersEscapingReverseMap;

    
    public function escape(string $string): string
    {
        $this->initIfNeeded();

        $escapedString = $this->escapeControlCharacters($string);

        
        
        return htmlspecialchars($escapedString, ENT_QUOTES, 'UTF-8');
    }

    
    public function unescape(string $string): string
    {
        $this->initIfNeeded();

        
        
        
        
        
        
        return $this->unescapeControlCharacters($string);
    }

    
    private function initIfNeeded(): void
    {
        if (!$this->isAlreadyInitialized) {
            $this->escapableControlCharactersPattern = $this->getEscapableControlCharactersPattern();
            $this->controlCharactersEscapingMap = $this->getControlCharactersEscapingMap();
            $this->controlCharactersEscapingReverseMap = array_flip($this->controlCharactersEscapingMap);

            $this->isAlreadyInitialized = true;
        }
    }

    
    private function getEscapableControlCharactersPattern(): string
    {
        
        
        return '[\x00-\x08'.
                
                '\x0B-\x0C'.
                
                '\x0E-\x1F]';
    }

    
    private function getControlCharactersEscapingMap(): array
    {
        $controlCharactersEscapingMap = [];

        
        for ($charValue = 0x00; $charValue <= 0x1F; ++$charValue) {
            $character = \chr($charValue);
            if (1 === preg_match("/{$this->escapableControlCharactersPattern}/", $character)) {
                $charHexValue = dechex($charValue);
                $escapedChar = '_x'.sprintf('%04s', strtoupper($charHexValue)).'_';
                $controlCharactersEscapingMap[$escapedChar] = $character;
            }
        }

        return $controlCharactersEscapingMap;
    }

    
    private function escapeControlCharacters(string $string): string
    {
        $escapedString = $this->escapeEscapeCharacter($string);

        
        if (1 !== preg_match("/{$this->escapableControlCharactersPattern}/", $escapedString)) {
            return $escapedString;
        }

        return preg_replace_callback("/({$this->escapableControlCharactersPattern})/", function ($matches) {
            return $this->controlCharactersEscapingReverseMap[$matches[0]];
        }, $escapedString);
    }

    
    private function escapeEscapeCharacter(string $string): string
    {
        return preg_replace('/_(x[\dA-F]{4})_/', '_x005F_$1_', $string);
    }

    
    private function unescapeControlCharacters(string $string): string
    {
        $unescapedString = $string;

        foreach ($this->controlCharactersEscapingMap as $escapedCharValue => $charValue) {
            
            $unescapedString = preg_replace("/(?<!_x005F)({$escapedCharValue})/", $charValue, $unescapedString);
        }

        return $this->unescapeEscapeCharacter($unescapedString);
    }

    
    private function unescapeEscapeCharacter(string $string): string
    {
        return preg_replace('/_x005F(_x[\dA-F]{4}_)/', '$1', $string);
    }
}
