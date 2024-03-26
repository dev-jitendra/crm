<?php


namespace Espo\Core\Utils;

use Espo\Core\Utils\File\Manager as FileManager;
use Espo\Core\Utils\Resource\FileReader;
use Espo\Core\Utils\Resource\FileReader\Params as FileReaderParams;

class TemplateFileManager
{
    public function __construct(
        private Config $config,
        private FileManager $fileManager,
        private FileReader $fileReader
    ) {}

    public function getTemplate(
        string $type,
        string $name,
        ?string $entityType = null,
        ?string $defaultModuleName = null
    ): string {

        $params = FileReaderParams::create()
            ->withScope($entityType)
            ->withModuleName($defaultModuleName);

        if ($entityType) {
            $path1 = $this->getPath($type, $name, $entityType);

            $exists1 = $this->fileReader->exists($path1, $params);

            if ($exists1) {
                return $this->fileReader->read($path1, $params);
            }
        }

        $path2 = $this->getPath($type, $name);

        $exists2 = $this->fileReader->exists($path2, $params);

        if ($exists2) {
            return $this->fileReader->read($path2, $params);
        }

        if ($entityType) {
            $path3 = $this->getDefaultLanguagePath($type, $name, $entityType);

            $exists3 = $this->fileReader->exists($path3, $params);

            if ($exists3) {
                return $this->fileReader->read($path3, $params);
            }
        }

        $path4 = $this->getDefaultLanguagePath($type, $name);

        return $this->fileReader->read($path4, $params);
    }

    public function saveTemplate(
        string $type,
        string $name,
        string $contents,
        ?string $entityType = null
    ): void {

        $language = $this->config->get('language');

        $filePath = $this->getCustomFilePath($language, $type, $name, $entityType);

        $this->fileManager->putContents($filePath, $contents);
    }

    public function resetTemplate(string $type, string $name, ?string $entityType = null): void
    {
        $language = $this->config->get('language');

        $filePath = $this->getCustomFilePath($language, $type, $name, $entityType);

        $this->fileManager->removeFile($filePath);
    }

    private function getCustomFilePath(
        string $language,
        string $type,
        string $name,
        ?string $entityType = null
    ): string {

        if ($entityType) {
            return "custom/Espo/Custom/Resources/templates/{$type}/{$language}/{$entityType}/{$name}.tpl";
        }

        return "custom/Espo/Custom/Resources/templates/{$type}/{$language}/{$name}.tpl";
    }

    private function getPath(string $type, string $name, ?string $entityType = null): string
    {
        $language = $this->config->get('language');

        return $this->getPathForLanguage($language, $type, $name, $entityType);
    }

    private function getDefaultLanguagePath(string $type, string $name, ?string $entityType = null): string
    {
        $language = 'en_US';

        return $this->getPathForLanguage($language, $type, $name, $entityType);
    }

    private function getPathForLanguage(
        string $language,
        string $type,
        string $name,
        ?string $entityType = null
    ): string {

        if ($entityType) {
            return "templates/{$type}/{$language}/{$entityType}/{$name}.tpl";
        }

        return "templates/{$type}/{$language}/{$name}.tpl";
    }
}
