<?php


namespace Espo\Core\Utils\Config;

use Espo\Core\Utils\Config;
use Espo\Core\Utils\File\Manager as FileManager;

use RuntimeException;

class ConfigFileManager
{
    protected FileManager $fileManager;

    public function __construct()
    {
        $this->fileManager = new FileManager();
    }

    public function setConfig(Config $config): void
    {
        $this->fileManager = new FileManager(
            $config->get('defaultPermissions')
        );
    }

    public function isFile(string $filePath): bool
    {
        return $this->fileManager->isFile($filePath);
    }

    
    protected function putPhpContentsInternal(string $path, array $data, bool $useRenaming = false): void
    {
        $result = $this->fileManager->putPhpContents($path, $data, true, $useRenaming);

        if ($result === false) {
            throw new RuntimeException();
        }
    }

    
    public function putPhpContents(string $path, array $data): void
    {
        $this->putPhpContentsInternal($path, $data, true);
    }

    
    public function putPhpContentsNoRenaming(string $path, array $data): void
    {
        $this->putPhpContentsInternal($path, $data, false);
    }

    
    public function getPhpContents(string $path): array
    {
        $data = $this->fileManager->getPhpContents($path);

        if (!is_array($data)) {
            throw new RuntimeException("Bad data stored in '{$path}.");
        }

        
        return $data;
    }
}
