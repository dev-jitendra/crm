<?php

namespace PhpOffice\PhpSpreadsheet\Shared;

class XMLWriter extends \XMLWriter
{
    public static $debugEnabled = false;

    
    const STORAGE_MEMORY = 1;
    const STORAGE_DISK = 2;

    
    private $tempFileName = '';

    
    public function __construct($pTemporaryStorage = self::STORAGE_MEMORY, $pTemporaryStorageFolder = null)
    {
        
        if ($pTemporaryStorage == self::STORAGE_MEMORY) {
            $this->openMemory();
        } else {
            
            if ($pTemporaryStorageFolder === null) {
                $pTemporaryStorageFolder = File::sysGetTempDir();
            }
            $this->tempFileName = @tempnam($pTemporaryStorageFolder, 'xml');

            
            if ($this->openUri($this->tempFileName) === false) {
                
                $this->openMemory();
            }
        }

        
        if (self::$debugEnabled) {
            $this->setIndent(true);
        }
    }

    
    public function __destruct()
    {
        
        if ($this->tempFileName != '') {
            @unlink($this->tempFileName);
        }
    }

    
    public function getData()
    {
        if ($this->tempFileName == '') {
            return $this->outputMemory(true);
        }
        $this->flush();

        return file_get_contents($this->tempFileName);
    }

    
    public function writeRawData($text)
    {
        if (is_array($text)) {
            $text = implode("\n", $text);
        }

        return $this->writeRaw(htmlspecialchars($text));
    }
}
