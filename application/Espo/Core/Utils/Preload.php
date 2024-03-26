<?php


namespace Espo\Core\Utils;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;
use RecursiveRegexIterator;


class Preload
{
    
    protected $dirList = [
        'application',
        'vendor/slim',
        'vendor/nikic/fast-route',
    ];

    private int $counter = 0;

    
    protected $ignoreList = [
        'application/Espo/Core/Mail/Parsers/PhpMimeMailParser/',
        'vendor/nikic/fast-route/test/',
    ];

    public function process(): void
    {
        foreach ($this->dirList as $dir) {
            $this->processForDir($dir);
        }
    }

    public function getCount(): int
    {
        return $this->counter;
    }

    private function processForDir(string $dir): void
    {
        $directory = new RecursiveDirectoryIterator($dir);
        $fullTree = new RecursiveIteratorIterator($directory);
        $phpFiles = new RegexIterator($fullTree, '/.+((?<!Test)+\.php$)/i', RecursiveRegexIterator::GET_MATCH);

        foreach ($phpFiles as $key => $file) {
            $this->processFile($file[0]);
        }
    }

    private function processFile(string $file): void
    {
        if ($this->isFileToBeIgnored($file)) {
            return;
        }

        require_once($file);

        $this->counter++;
    }

    private function isFileToBeIgnored(string $file): bool
    {
        $file = str_replace('\\', '/', $file);

        foreach ($this->ignoreList as $item) {
            if (str_starts_with($file, $item)) {
                return true;
            }
        }

        if (str_contains($file, 'vendor/composer/ClassLoader.php')) {
            return true;
        }

        return false;
    }
}
