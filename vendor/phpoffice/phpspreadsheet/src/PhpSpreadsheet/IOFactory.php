<?php

namespace PhpOffice\PhpSpreadsheet;

use PhpOffice\PhpSpreadsheet\Shared\File;


abstract class IOFactory
{
    private static $readers = [
        'Xlsx' => Reader\Xlsx::class,
        'Xls' => Reader\Xls::class,
        'Xml' => Reader\Xml::class,
        'Ods' => Reader\Ods::class,
        'Slk' => Reader\Slk::class,
        'Gnumeric' => Reader\Gnumeric::class,
        'Html' => Reader\Html::class,
        'Csv' => Reader\Csv::class,
    ];

    private static $writers = [
        'Xls' => Writer\Xls::class,
        'Xlsx' => Writer\Xlsx::class,
        'Ods' => Writer\Ods::class,
        'Csv' => Writer\Csv::class,
        'Html' => Writer\Html::class,
        'Tcpdf' => Writer\Pdf\Tcpdf::class,
        'Dompdf' => Writer\Pdf\Dompdf::class,
        'Mpdf' => Writer\Pdf\Mpdf::class,
    ];

    
    public static function createWriter(Spreadsheet $spreadsheet, $writerType)
    {
        if (!isset(self::$writers[$writerType])) {
            throw new Writer\Exception("No writer found for type $writerType");
        }

        
        $className = self::$writers[$writerType];

        return new $className($spreadsheet);
    }

    
    public static function createReader($readerType)
    {
        if (!isset(self::$readers[$readerType])) {
            throw new Reader\Exception("No reader found for type $readerType");
        }

        
        $className = self::$readers[$readerType];

        return new $className();
    }

    
    public static function load($pFilename)
    {
        $reader = self::createReaderForFile($pFilename);

        return $reader->load($pFilename);
    }

    
    public static function identify($pFilename)
    {
        $reader = self::createReaderForFile($pFilename);
        $className = get_class($reader);
        $classType = explode('\\', $className);
        unset($reader);

        return array_pop($classType);
    }

    
    public static function createReaderForFile($filename)
    {
        File::assertFile($filename);

        
        $guessedReader = self::getReaderTypeFromExtension($filename);
        if ($guessedReader !== null) {
            $reader = self::createReader($guessedReader);

            
            if (isset($reader) && $reader->canRead($filename)) {
                return $reader;
            }
        }

        
        
        foreach (self::$readers as $type => $class) {
            
            if ($type !== $guessedReader) {
                $reader = self::createReader($type);
                if ($reader->canRead($filename)) {
                    return $reader;
                }
            }
        }

        throw new Reader\Exception('Unable to identify a reader for this file');
    }

    
    private static function getReaderTypeFromExtension($filename)
    {
        $pathinfo = pathinfo($filename);
        if (!isset($pathinfo['extension'])) {
            return null;
        }

        switch (strtolower($pathinfo['extension'])) {
            case 'xlsx': 
            case 'xlsm': 
            case 'xltx': 
            case 'xltm': 
                return 'Xlsx';
            case 'xls': 
            case 'xlt': 
                return 'Xls';
            case 'ods': 
            case 'ots': 
                return 'Ods';
            case 'slk':
                return 'Slk';
            case 'xml': 
                return 'Xml';
            case 'gnumeric':
                return 'Gnumeric';
            case 'htm':
            case 'html':
                return 'Html';
            case 'csv':
                
                
                
                return null;
            default:
                return null;
        }
    }

    
    public static function registerWriter($writerType, $writerClass): void
    {
        if (!is_a($writerClass, Writer\IWriter::class, true)) {
            throw new Writer\Exception('Registered writers must implement ' . Writer\IWriter::class);
        }

        self::$writers[$writerType] = $writerClass;
    }

    
    public static function registerReader($readerType, $readerClass): void
    {
        if (!is_a($readerClass, Reader\IReader::class, true)) {
            throw new Reader\Exception('Registered readers must implement ' . Reader\IReader::class);
        }

        self::$readers[$readerType] = $readerClass;
    }
}
