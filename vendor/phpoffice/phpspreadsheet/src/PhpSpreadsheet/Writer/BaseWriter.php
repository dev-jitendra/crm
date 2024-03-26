<?php

namespace PhpOffice\PhpSpreadsheet\Writer;

abstract class BaseWriter implements IWriter
{
    
    protected $includeCharts = false;

    
    protected $preCalculateFormulas = true;

    
    private $useDiskCaching = false;

    
    private $diskCachingDirectory = './';

    
    protected $fileHandle;

    
    private $shouldCloseFile;

    public function getIncludeCharts()
    {
        return $this->includeCharts;
    }

    public function setIncludeCharts($pValue)
    {
        $this->includeCharts = (bool) $pValue;

        return $this;
    }

    public function getPreCalculateFormulas()
    {
        return $this->preCalculateFormulas;
    }

    public function setPreCalculateFormulas($pValue)
    {
        $this->preCalculateFormulas = (bool) $pValue;

        return $this;
    }

    public function getUseDiskCaching()
    {
        return $this->useDiskCaching;
    }

    public function setUseDiskCaching($pValue, $pDirectory = null)
    {
        $this->useDiskCaching = $pValue;

        if ($pDirectory !== null) {
            if (is_dir($pDirectory)) {
                $this->diskCachingDirectory = $pDirectory;
            } else {
                throw new Exception("Directory does not exist: $pDirectory");
            }
        }

        return $this;
    }

    public function getDiskCachingDirectory()
    {
        return $this->diskCachingDirectory;
    }

    
    public function openFileHandle($filename): void
    {
        if (is_resource($filename)) {
            $this->fileHandle = $filename;
            $this->shouldCloseFile = false;

            return;
        }

        $fileHandle = $filename ? fopen($filename, 'wb+') : false;
        if ($fileHandle === false) {
            throw new Exception('Could not open file "' . $filename . '" for writing.');
        }

        $this->fileHandle = $fileHandle;
        $this->shouldCloseFile = true;
    }

    
    protected function maybeCloseFileHandle(): void
    {
        if ($this->shouldCloseFile) {
            if (!fclose($this->fileHandle)) {
                throw new Exception('Could not close file after writing.');
            }
        }
    }
}
