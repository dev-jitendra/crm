<?php


namespace Espo\Core\Utils\File;

use Espo\Core\Utils\DataUtil;
use Espo\Core\Utils\File\Manager as FileManager;
use Espo\Core\Utils\Json;
use Espo\Core\Utils\Module;
use Espo\Core\Utils\Resource\PathProvider;
use Espo\Core\Utils\Util;

use JsonException;
use LogicException;
use stdClass;

class Unifier
{
    protected bool $useObjects = false;
    private string $unsetFileName = 'unset.json';
    private const APPEND_VALUE = '__APPEND__';
    private const ANY_KEY = '__ANY__';

    public function __construct(
        private FileManager $fileManager,
        private Module $module,
        private PathProvider $pathProvider
    ) {}

    
    public function unify(string $path, bool $noCustom = false, array $forceAppendPathList = [])
    {
        if ($this->useObjects) {
            return $this->unifyObject($path, $noCustom, $forceAppendPathList);
        }

        return $this->unifyArray($path, $noCustom);
    }

    
    private function unifyArray(string $path, bool $noCustom = false)
    {
        
        $data = $this->unifySingle($this->pathProvider->getCore() . $path, true);

        foreach ($this->getModuleList() as $moduleName) {
            $filePath = $this->pathProvider->getModule($moduleName) . $path;

            
            $newData = $this->unifySingle($filePath, true);

            
            $data = Util::merge($data, $newData);
        }

        if ($noCustom) {
            return $data;
        }

        $customFilePath = $this->pathProvider->getCustom() . $path;

        
        $newData = $this->unifySingle($customFilePath, true);

        
        return Util::merge($data, $newData);
    }

    
    private function unifyObject(string $path, bool $noCustom = false, array $forceAppendPathList = [])
    {
        
        $data = $this->unifySingle($this->pathProvider->getCore() . $path, true);

        foreach ($this->getModuleList() as $moduleName) {
            $filePath = $this->pathProvider->getModule($moduleName) . $path;

            
            $itemData = $this->unifySingle($filePath, true);

            $this->prepareItemDataObject($itemData, $forceAppendPathList);

            
            $data = DataUtil::merge($data, $itemData);
        }

        if ($noCustom) {
            return $data;
        }

        $customFilePath = $this->pathProvider->getCustom() . $path;

        
        $itemData = $this->unifySingle($customFilePath, true);

        $this->prepareItemDataObject($itemData, $forceAppendPathList);

        
        return DataUtil::merge($data, $itemData);
    }

    
    private function unifySingle(string $dirPath, bool $recursively)
    {
        $data = [];
        $unsets = [];

        if ($this->useObjects) {
            $data = (object) [];
        }

        if (empty($dirPath) || !$this->fileManager->exists($dirPath)) {
            return $data;
        }

        $fileList = $this->fileManager->getFileList($dirPath, $recursively, '\.json$');

        

        foreach ($fileList as $dirName => $item) {
            if (is_array($item)) {
                
                
                $itemValue = $this->unifySingle(
                    Util::concatPath($dirPath, $dirName),
                    false
                );

                if ($this->useObjects) {
                    

                    $data->$dirName = $itemValue;

                    continue;
                }

                

                $data[$dirName] = $itemValue;

                continue;
            }

            

            $fileName = $item;

            if ($fileName === $this->unsetFileName) {
                $fileContent = $this->fileManager->getContents($dirPath . '/' . $fileName);

                $unsets = Json::decode($fileContent, true);

                continue;
            }

            $itemValue = $this->getContents($dirPath . '/' . $fileName);

            if (empty($itemValue)) {
                continue;
            }

            $name = $this->fileManager->getFileName($fileName, '.json');

            if ($this->useObjects) {
                

                $data->$name = $itemValue;

                continue;
            }

            

            $data[$name] = $itemValue;
        }

        if ($this->useObjects) {
            

            
            return DataUtil::unsetByKey($data, $unsets);
        }

        

        
        return Util::unsetInArray($data, $unsets);
    }

    
    private function getContents(string $path)
    {
        $fileContent = $this->fileManager->getContents($path);

        try {
            return Json::decode($fileContent, !$this->useObjects);
        }
        catch (JsonException) {
            throw new JsonException("JSON syntax error in '$path'.");
        }
    }

    
    private function getModuleList(): array
    {
        return $this->module->getOrderedList();
    }

    
    private function prepareItemDataObject(stdClass $data, array $forceAppendPathList): void
    {
        foreach ($forceAppendPathList as $path) {
            $this->addAppendToData($data, $path);
        }
    }

    
    private function addAppendToData(stdClass $data, array $path): void
    {
        if (count($path) === 0) {
            return;
        }

        $nextPath = array_slice($path, 1);

        $key = $path[0];

        if ($key === self::ANY_KEY) {
            foreach (array_keys(get_object_vars($data)) as $itemKey) {
                $this->addAppendToDataItem($data, $itemKey, $nextPath);
            }

            return;
        }

        $this->addAppendToDataItem($data, $key, $nextPath);
    }

    
    private function addAppendToDataItem(stdClass $data, string $key, array $path): void
    {
        $item = $data->$key ?? null;

        if (count($path) === 0) {
            if ($item === null) {
                $item = [];
            }

            if (!is_array($item)) {
                throw new LogicException("Expected array in metadata, but non-array is set.");
            }

            if (($item[0] ?? null) === self::APPEND_VALUE) {
                return;
            }

            $data->$key = array_merge([self::APPEND_VALUE], $item);

            return;
        }

        if (!$item instanceof stdClass) {
            return;
        }

        $this->addAppendToData($item, $path);
    }
}
