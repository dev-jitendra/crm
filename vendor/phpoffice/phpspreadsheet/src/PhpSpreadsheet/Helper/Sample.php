<?php

namespace PhpOffice\PhpSpreadsheet\Helper;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\IWriter;
use PhpOffice\PhpSpreadsheet\Writer\Pdf;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use ReflectionClass;
use RegexIterator;
use RuntimeException;


class Sample
{
    
    public function isCli()
    {
        return PHP_SAPI === 'cli';
    }

    
    public function getScriptFilename()
    {
        return basename($_SERVER['SCRIPT_FILENAME'], '.php');
    }

    
    public function isIndex()
    {
        return $this->getScriptFilename() === 'index';
    }

    
    public function getPageTitle()
    {
        return $this->isIndex() ? 'PHPSpreadsheet' : $this->getScriptFilename();
    }

    
    public function getPageHeading()
    {
        return $this->isIndex() ? '' : '<h1>' . str_replace('_', ' ', $this->getScriptFilename()) . '</h1>';
    }

    
    public function getSamples()
    {
        
        $baseDir = realpath(__DIR__ . '/../../../samples');
        $directory = new RecursiveDirectoryIterator($baseDir);
        $iterator = new RecursiveIteratorIterator($directory);
        $regex = new RegexIterator($iterator, '/^.+\.php$/', RecursiveRegexIterator::GET_MATCH);

        $files = [];
        foreach ($regex as $file) {
            $file = str_replace(str_replace('\\', '/', $baseDir) . '/', '', str_replace('\\', '/', $file[0]));
            $info = pathinfo($file);
            $category = str_replace('_', ' ', $info['dirname']);
            $name = str_replace('_', ' ', preg_replace('/(|\.php)/', '', $info['filename']));
            if (!in_array($category, ['.', 'boostrap', 'templates'])) {
                if (!isset($files[$category])) {
                    $files[$category] = [];
                }
                $files[$category][$name] = $file;
            }
        }

        
        ksort($files);
        foreach ($files as &$f) {
            asort($f);
        }

        return $files;
    }

    
    public function write(Spreadsheet $spreadsheet, $filename, array $writers = ['Xlsx', 'Xls']): void
    {
        
        $spreadsheet->setActiveSheetIndex(0);

        
        foreach ($writers as $writerType) {
            $path = $this->getFilename($filename, mb_strtolower($writerType));
            $writer = IOFactory::createWriter($spreadsheet, $writerType);
            if ($writer instanceof Pdf) {
                
                $tempDir = $this->getTemporaryFolder();
                $writer->setTempDir($tempDir);
            }
            $callStartTime = microtime(true);
            $writer->save($path);
            $this->logWrite($writer, $path, $callStartTime);
        }

        $this->logEndingNotes();
    }

    
    private function getTemporaryFolder()
    {
        $tempFolder = sys_get_temp_dir() . '/phpspreadsheet';
        if (!is_dir($tempFolder)) {
            if (!mkdir($tempFolder) && !is_dir($tempFolder)) {
                throw new RuntimeException(sprintf('Directory "%s" was not created', $tempFolder));
            }
        }

        return $tempFolder;
    }

    
    public function getFilename($filename, $extension = 'xlsx')
    {
        $originalExtension = pathinfo($filename, PATHINFO_EXTENSION);

        return $this->getTemporaryFolder() . '/' . str_replace('.' . $originalExtension, '.' . $extension, basename($filename));
    }

    
    public function getTemporaryFilename($extension = 'xlsx')
    {
        $temporaryFilename = tempnam($this->getTemporaryFolder(), 'phpspreadsheet-');
        unlink($temporaryFilename);

        return $temporaryFilename . '.' . $extension;
    }

    public function log($message): void
    {
        $eol = $this->isCli() ? PHP_EOL : '<br />';
        echo date('H:i:s ') . $message . $eol;
    }

    
    public function logEndingNotes(): void
    {
        
        $this->log('Peak memory usage: ' . (memory_get_peak_usage(true) / 1024 / 1024) . 'MB');
    }

    
    public function logWrite(IWriter $writer, $path, $callStartTime): void
    {
        $callEndTime = microtime(true);
        $callTime = $callEndTime - $callStartTime;
        $reflection = new ReflectionClass($writer);
        $format = $reflection->getShortName();
        $message = "Write {$format} format to <code>{$path}</code>  in " . sprintf('%.4f', $callTime) . ' seconds';

        $this->log($message);
    }

    
    public function logRead($format, $path, $callStartTime): void
    {
        $callEndTime = microtime(true);
        $callTime = $callEndTime - $callStartTime;
        $message = "Read {$format} format from <code>{$path}</code>  in " . sprintf('%.4f', $callTime) . ' seconds';

        $this->log($message);
    }
}
