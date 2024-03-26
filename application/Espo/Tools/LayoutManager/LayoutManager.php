<?php


namespace Espo\Tools\LayoutManager;

use Espo\Core\Exceptions\Error;
use Espo\Core\Utils\File\Manager as FileManager;
use Espo\Core\Utils\Json;
use Espo\Tools\Layout\LayoutProvider;

use const JSON_PRETTY_PRINT;
use const JSON_UNESCAPED_UNICODE;

class LayoutManager
{
    
    protected $changedData = [];

    public function __construct(
        protected FileManager $fileManager,
        protected LayoutProvider $layoutProvider
    ) {}

    
    public function get(string $scope, string $name): ?string
    {
        return $this->layoutProvider->get($scope, $name);
    }

    
    public function set($data, string $scope, string $name): void
    {
        $scope = $this->sanitizeInput($scope);
        $name = $this->sanitizeInput($name);

        if (empty($scope) || empty($name)) {
            throw new Error("Error while setting layout.");
        }

        $this->changedData[$scope][$name] = $data;
    }

    public function resetToDefault(string $scope, string $name): ?string
    {
        $scope = $this->sanitizeInput($scope);
        $name = $this->sanitizeInput($name);

        $filePath = 'custom/Espo/Custom/Resources/layouts/' . $scope . '/' . $name . '.json';

        if ($this->fileManager->isFile($filePath)) {
            $this->fileManager->removeFile($filePath);
        }

        if (!empty($this->changedData[$scope]) && !empty($this->changedData[$scope][$name])) {
            unset($this->changedData[$scope][$name]);
        }

        return $this->get($scope, $name);
    }

    
    public function save(): void
    {
        $result = true;

        if (empty($this->changedData)) {
            return;
        }

        foreach ($this->changedData as $scope => $rowData) {
            $dirPath = 'custom/Espo/Custom/Resources/layouts/' . $scope . '/';

            foreach ($rowData as $layoutName => $layoutData) {
                if (empty($scope) || empty($layoutName)) {
                    continue;
                }

                $data = Json::encode($layoutData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

                $path = $dirPath . $layoutName . '.json';

                $result &= $this->fileManager->putContents($path, $data);
            }
        }

        if (!$result) {
            throw new Error("Error while saving layout.");
        }

        $this->clearChanges();
    }

    
    public function clearChanges(): void
    {
        $this->changedData = [];
    }

    protected function sanitizeInput(string $name): string
    {
        
        return preg_replace("([.]{2,})", '', $name);
    }
}
