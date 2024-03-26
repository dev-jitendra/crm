<?php


namespace Espo\Core\Utils\Autoload;

use Espo\Core\Utils\File\Manager as FileManager;

class Loader
{
    public function __construct(
        private NamespaceLoader $namespaceLoader,
        private FileManager $fileManager
    ) {}

    
    public function register(array $data): void
    {
        
        $this->namespaceLoader->register($data);

        
        $this->registerAutoloadFileList($data);

        
        $this->registerFiles($data);
    }

    
    private function registerAutoloadFileList(array $data): void
    {
        $keyName = 'autoloadFileList';

        if (!isset($data[$keyName])) {
            return;
        }

        foreach ($data[$keyName] as $filePath) {
            if ($this->fileManager->exists($filePath)) {
                require_once($filePath);
            }
        }
    }

    
    private function registerFiles(array $data): void
    {
        $keyName = 'files';

        if (!isset($data[$keyName])) {
            return;
        }

        foreach ($data[$keyName] as $filePath) {
            if ($this->fileManager->exists($filePath)) {
                require_once($filePath);
            }
        }
    }
}
