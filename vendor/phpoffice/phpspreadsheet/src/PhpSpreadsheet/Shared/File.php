<?php

namespace PhpOffice\PhpSpreadsheet\Shared;

use InvalidArgumentException;
use ZipArchive;

class File
{
    
    protected static $useUploadTempDirectory = false;

    
    public static function setUseUploadTempDirectory($useUploadTempDir): void
    {
        self::$useUploadTempDirectory = (bool) $useUploadTempDir;
    }

    
    public static function getUseUploadTempDirectory()
    {
        return self::$useUploadTempDirectory;
    }

    
    public static function fileExists($pFilename)
    {
        
        
        
        if (strtolower(substr($pFilename, 0, 3)) == 'zip') {
            
            $zipFile = substr($pFilename, 6, strpos($pFilename, '#') - 6);
            $archiveFile = substr($pFilename, strpos($pFilename, '#') + 1);

            $zip = new ZipArchive();
            if ($zip->open($zipFile) === true) {
                $returnValue = ($zip->getFromName($archiveFile) !== false);
                $zip->close();

                return $returnValue;
            }

            return false;
        }

        return file_exists($pFilename);
    }

    
    public static function realpath($pFilename)
    {
        
        $returnValue = '';

        
        if (file_exists($pFilename)) {
            $returnValue = realpath($pFilename);
        }

        
        if ($returnValue == '' || ($returnValue === null)) {
            $pathArray = explode('/', $pFilename);
            while (in_array('..', $pathArray) && $pathArray[0] != '..') {
                $iMax = count($pathArray);
                for ($i = 0; $i < $iMax; ++$i) {
                    if ($pathArray[$i] == '..' && $i > 0) {
                        unset($pathArray[$i], $pathArray[$i - 1]);

                        break;
                    }
                }
            }
            $returnValue = implode('/', $pathArray);
        }

        
        return $returnValue;
    }

    
    public static function sysGetTempDir()
    {
        if (self::$useUploadTempDirectory) {
            
            
            if (ini_get('upload_tmp_dir') !== false) {
                if ($temp = ini_get('upload_tmp_dir')) {
                    if (file_exists($temp)) {
                        return realpath($temp);
                    }
                }
            }
        }

        return realpath(sys_get_temp_dir());
    }

    
    public static function assertFile($filename): void
    {
        if (!is_file($filename)) {
            throw new InvalidArgumentException('File "' . $filename . '" does not exist.');
        }

        if (!is_readable($filename)) {
            throw new InvalidArgumentException('Could not open "' . $filename . '" for reading.');
        }
    }
}
