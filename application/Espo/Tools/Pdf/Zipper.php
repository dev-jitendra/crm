<?php


namespace Espo\Tools\Pdf;

use Espo\Core\Utils\Util;
use LogicException;
use RuntimeException;
use ZipArchive;

class Zipper
{
    private ?string $filePath = null;
    
    private array $itemList = [];

    public function __construct() {}

    public function add(Contents $contents, string $name): void
    {
        $tempPath = tempnam(sys_get_temp_dir(), 'espo-pdf-zip-item');

        if ($tempPath === false) {
            throw new RuntimeException("Could not create a temp file.");
        }

        $fp = fopen($tempPath, 'w');

        if ($fp === false) {
            throw new RuntimeException("Could not open a temp file {$tempPath}.");
        }

        fwrite($fp, $contents->getString());
        fclose($fp);

        $this->itemList[] = [$tempPath, Util::sanitizeFileName($name) . '.pdf'];
    }

    public function archive(): void
    {
        $tempPath = tempnam(sys_get_temp_dir(), 'espo-pdf-zip');

        if ($tempPath === false) {
            throw new RuntimeException("Could not create a temp file.");
        }

        $archive = new ZipArchive();
        $archive->open($tempPath, ZipArchive::CREATE);

        foreach ($this->itemList as $item) {
            $archive->addFile($item[0], $item[1]);
        }

        $archive->close();

        $this->filePath = $tempPath;
    }

    public function getFilePath(): string
    {
        if (!$this->filePath) {
            throw new LogicException();
        }

        return $this->filePath;
    }
}
