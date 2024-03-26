<?php


namespace Espo\Core\Utils\Config;

use Espo\Core\Utils\Config;
use Espo\Core\Utils\File\Manager as FileManager;

use RuntimeException;

class ConfigWriterFileManager
{
    private FileManager $fileManager;

    
    public function __construct(?Config $config = null, ?array $defaultPermissions = null)
    {
        $defaultPermissionsToSet = null;

        if ($defaultPermissions) {
            $defaultPermissionsToSet = $defaultPermissions;
        }
        else if ($config) {
            $defaultPermissionsToSet = $config->get('defaultPermissions');
        }

        $this->fileManager = new FileManager($defaultPermissionsToSet);
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


    
    public function getPhpContents(string $path)
    {
        try {
            $data = $this->fileManager->getPhpContents($path);
        }
        catch (RuntimeException) {
            return false;
        }

        if (!is_array($data)) {
            return false;
        }

        
        return $data;
    }
}
