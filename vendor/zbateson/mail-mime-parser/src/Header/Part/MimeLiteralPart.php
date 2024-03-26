<?php

namespace ZBateson\MailMimeParser\Header\Part;

use ZBateson\MbWrapper\MbWrapper;


class MimeLiteralPart extends LiteralPart
{
    
    const MIME_PART_PATTERN = '=\?[^?=]+\?[QBqb]\?[^\?]+\?=';

    
    const MIME_PART_PATTERN_NO_QUOTES = '=\?[^\?=]+\?[QBqb]\?[^\?"]+\?=';
    
    
    protected $canIgnoreSpacesBefore = false;
    
    
    protected $canIgnoreSpacesAfter = false;
    
    
    protected $languages = [];
    
    
    public function __construct(MbWrapper $charsetConverter, $token)
    {
        parent::__construct($charsetConverter);
        $this->value = $this->decodeMime($token);
        
        $pattern = self::MIME_PART_PATTERN;
        $this->canIgnoreSpacesBefore = (bool) preg_match("/^\s*{$pattern}/", $token);
        $this->canIgnoreSpacesAfter = (bool) preg_match("/{$pattern}\s*\$/", $token);
    }
    
    
    protected function decodeMime($value)
    {
        $pattern = self::MIME_PART_PATTERN;
        
        $value = preg_replace("/($pattern)\\s+(?=$pattern)/", '$1', $value);
        
        $aMimeParts = preg_split("/($pattern)/", $value, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        $ret = '';
        foreach ($aMimeParts as $entity) {
            $ret .= $this->decodeSplitPart($entity);
        }
        return $ret;
    }
    
    
    private function decodeMatchedEntity($matches)
    {
        $body = $matches[4];
        if (strtoupper($matches[3]) === 'Q') {
            $body = quoted_printable_decode(str_replace('_', '=20', $body));
        } else {
            $body = base64_decode($body);
        }
        $language = $matches[2];
        $decoded = $this->convertEncoding($body, $matches[1], true);
        $this->addToLanguage($decoded, $language);
        return $decoded;
    }
    
    
    private function decodeSplitPart($entity)
    {
        if (preg_match("/^=\?([A-Za-z\-_0-9]+)\*?([A-Za-z\-_0-9]+)?\?([QBqb])\?([^\?]+)\?=$/", $entity, $matches)) {
            return $this->decodeMatchedEntity($matches);
        }
        $decoded = $this->convertEncoding($entity);
        $this->addToLanguage($decoded);
        return $decoded;
    }
    
    
    public function ignoreSpacesBefore()
    {
        return $this->canIgnoreSpacesBefore;
    }
    
    
    public function ignoreSpacesAfter()
    {
        return $this->canIgnoreSpacesAfter;
    }
    
    
    protected function addToLanguage($part, $language = null)
    {
        $this->languages[] = [
            'lang' => $language,
            'value' => $part
        ];
    }
    
    
    public function getLanguageArray()
    {
        return $this->languages;
    }
}
