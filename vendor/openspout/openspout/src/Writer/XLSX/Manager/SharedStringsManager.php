<?php

declare(strict_types=1);

namespace OpenSpout\Writer\XLSX\Manager;

use OpenSpout\Common\Helper\Escaper;


final class SharedStringsManager
{
    public const SHARED_STRINGS_FILE_NAME = 'sharedStrings.xml';

    public const SHARED_STRINGS_XML_FILE_FIRST_PART_HEADER = <<<'EOD'
        <?xml version="1.0" encoding="UTF-8" standalone="yes"?>
        <sst xmlns="http:
        EOD;

    
    public const DEFAULT_STRINGS_COUNT_PART = 'count="9999999999999" uniqueCount="9999999999999"';

    
    private $sharedStringsFilePointer;

    
    private int $numSharedStrings = 0;

    
    private readonly Escaper\XLSX $stringsEscaper;

    
    public function __construct(string $xlFolder, Escaper\XLSX $stringsEscaper)
    {
        $sharedStringsFilePath = $xlFolder.\DIRECTORY_SEPARATOR.self::SHARED_STRINGS_FILE_NAME;
        $resource = fopen($sharedStringsFilePath, 'w');
        \assert(false !== $resource);
        $this->sharedStringsFilePointer = $resource;

        
        $header = self::SHARED_STRINGS_XML_FILE_FIRST_PART_HEADER.' '.self::DEFAULT_STRINGS_COUNT_PART.'>';
        fwrite($this->sharedStringsFilePointer, $header);

        $this->stringsEscaper = $stringsEscaper;
    }

    
    public function writeString(string $string): int
    {
        fwrite($this->sharedStringsFilePointer, '<si><t xml:space="preserve">'.$this->stringsEscaper->escape($string).'</t></si>');
        ++$this->numSharedStrings;

        
        return $this->numSharedStrings - 1;
    }

    
    public function close(): void
    {
        fwrite($this->sharedStringsFilePointer, '</sst>');

        
        $firstPartHeaderLength = \strlen(self::SHARED_STRINGS_XML_FILE_FIRST_PART_HEADER);
        $defaultStringsCountPartLength = \strlen(self::DEFAULT_STRINGS_COUNT_PART);

        
        fseek($this->sharedStringsFilePointer, $firstPartHeaderLength + 1);
        fwrite($this->sharedStringsFilePointer, sprintf("%-{$defaultStringsCountPartLength}s", 'count="'.$this->numSharedStrings.'" uniqueCount="'.$this->numSharedStrings.'"'));

        fclose($this->sharedStringsFilePointer);
    }
}
