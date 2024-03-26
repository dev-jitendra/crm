<?php


namespace Espo\Core\Utils\File;

use RuntimeException;

class ZipArchive
{
    private Manager $fileManager;

    public function __construct(?Manager $fileManager = null)
    {
        if ($fileManager === null) {
            $fileManager = new Manager();
        }

        $this->fileManager = $fileManager;
    }

    
    public function unzip($file, $destinationPath)
    {
        if (!class_exists('\ZipArchive')) {
            throw new RuntimeException("php-zip extension is not installed. Cannot unzip the file.");
        }

        $zip = new \ZipArchive;

        $res = $zip->open($file);

        if ($res === true) {
            $this->fileManager->mkdir($destinationPath);

            $zip->extractTo($destinationPath);
            $zip->close();

            return true;
        }

        return false;
    }
}
