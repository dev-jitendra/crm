<?php


namespace Espo\Core\Utils;

use Espo\Core\Utils\File\Manager as FileManager;

use InvalidArgumentException;
use RuntimeException;
use stdClass;

class DataCache
{
    protected string $cacheDir = 'data/cache/application/';

    public function __construct(protected FileManager $fileManager)
    {}

    
    public function has(string $key): bool
    {
        $cacheFile = $this->getCacheFile($key);

        return $this->fileManager->isFile($cacheFile);
    }

    
    public function get(string $key)
    {
        $cacheFile = $this->getCacheFile($key);

        return $this->fileManager->getPhpSafeContents($cacheFile);
    }

    
    public function store(string $key, $data): void
    {
        

        if (!$this->checkDataIsValid($data)) {
            throw new InvalidArgumentException("Bad cache data type.");
        }

        $cacheFile = $this->getCacheFile($key);

        $result = $this->fileManager->putPhpContents($cacheFile, $data, true, true);

        if ($result === false) {
            throw new RuntimeException("Could not store '{$key}'.");
        }
    }

    
    public function clear(string $key): void
    {
        $cacheFile = $this->getCacheFile($key);

        $this->fileManager->removeFile($cacheFile);
    }

    
    private function checkDataIsValid($data)
    {
        $isInvalid =
            !is_array($data) &&
            !$data instanceof stdClass;

        return !$isInvalid;
    }

    private function getCacheFile(string $key): string
    {
        if (
            $key === '' ||
            preg_match('/[^a-zA-Z0-9_\/\-]/i', $key) ||
            $key[0] === '/' ||
            str_ends_with($key, '/')
        ) {
            throw new InvalidArgumentException("Bad cache key.");
        }

        return $this->cacheDir . $key . '.php';
    }
}
