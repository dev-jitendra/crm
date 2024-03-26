<?php


namespace Espo\Core\Utils\Resource;

use Espo\Core\Utils\File\Manager as FileManager;
use Espo\Core\Utils\Metadata;
use Espo\Core\Utils\Resource\FileReader\Params;
use RuntimeException;


class FileReader
{
    public function __construct(
        private FileManager $fileManager,
        private Metadata $metadata,
        private PathProvider $pathProvider
    ) {}

    
    public function read(string $path, Params $params): string
    {
        $exactPath = $this->findExactPath($path, $params);

        if (!$exactPath) {
            throw new RuntimeException("Resource file '$path' does not exist.");
        }

        return $this->fileManager->getContents($exactPath);
    }

    
    public function exists(string $path, Params $params): bool
    {
        return $this->findExactPath($path, $params) !== null;
    }

    private function findExactPath(string $path, Params $params): ?string
    {
        $customPath = $this->pathProvider->getCustom() . $path;

        if ($this->fileManager->isFile($customPath)) {
            return $customPath;
        }

        $moduleName = null;

        if ($params->getScope()) {
            $moduleName = $this->metadata->getScopeModuleName($params->getScope());
        }

        if ($moduleName) {
            $modulePath = $this->buildModulePath($path, $moduleName);

            if ($this->fileManager->isFile($modulePath)) {
                return $modulePath;
            }
        }

        if ($params->getModuleName()) {
            $modulePath = $this->buildModulePath($path, $params->getModuleName());

            if ($this->fileManager->isFile($modulePath)) {
                return $modulePath;
            }
        }

        $corePath = $this->pathProvider->getCore() . $path;

        if ($this->fileManager->isFile($corePath)) {
            return $corePath;
        }

        return null;
    }

    private function buildModulePath(string $path, string $moduleName): string
    {
        return $this->pathProvider->getModule($moduleName) . $path;
    }
}
