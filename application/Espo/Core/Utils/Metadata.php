<?php


namespace Espo\Core\Utils;

use Espo\Core\Utils\File\Manager as FileManager;
use Espo\Core\Utils\Metadata\Builder;
use Espo\Core\Utils\Metadata\BuilderHelper;

use stdClass;
use LogicException;
use RuntimeException;


class Metadata
{
    
    private ?array $data = null;
    private ?stdClass $objData = null;

    private string $cacheKey = 'metadata';
    private string $objCacheKey = 'objMetadata';
    private string $customPath = 'custom/Espo/Custom/Resources/metadata';

    
    private $deletedData = [];
    
    private $changedData = [];

    public function __construct(
        private FileManager $fileManager,
        private DataCache $dataCache,
        private Module $module,
        private Builder $builder,
        private BuilderHelper $builderHelper,
        private bool $useCache = false
    ) {}

    
    public function init(bool $reload = false): void
    {
        if (!$this->useCache) {
            $reload = true;
        }

        if ($this->dataCache->has($this->cacheKey) && !$reload) {
            
            $data = $this->dataCache->get($this->cacheKey);

            $this->data = $data;

            return;
        }

        $this->clearVars();

        $objData = $this->getObjData($reload);

        $this->data = Util::objectToArray($objData);

        if ($this->useCache) {
            $this->dataCache->store($this->cacheKey, $this->data);
        }
    }

    
    private function getData(): array
    {
        if (empty($this->data) || !is_array($this->data)) {
            $this->init();
        }

        assert($this->data !== null);

        return $this->data;
    }

    
    public function get($key = null, $default = null)
    {
        return Util::getValueByKey($this->getData(), $key, $default);
    }

    private function objInit(bool $reload = false): void
    {
        if (!$this->useCache) {
            $reload = true;
        }

        if ($this->dataCache->has($this->objCacheKey) && !$reload) {
            
            $data = $this->dataCache->get($this->objCacheKey);

            $this->objData = $data;

            return;
        }

        $this->objData = $this->builder->build();

        if ($this->useCache) {
            $this->dataCache->store($this->objCacheKey, $this->objData);
        }
    }

    private function getObjData(bool $reload = false): stdClass
    {
        if (!isset($this->objData) || $reload) {
            $this->objInit($reload);
        }

        assert($this->objData !== null);

        return $this->objData;
    }

    
    public function getObjects($key = null, $default = null)
    {
        $objData = $this->getObjData();

        return Util::getValueByKey($objData, $key, $default);
    }

    public function getAll(): stdClass
    {
        return $this->getObjData();
    }



    
    public function getCustom(string $key1, string $key2, $default = null)
    {
        $filePath = $this->customPath . "/$key1/$key2.json";

        if (!$this->fileManager->isFile($filePath)) {
            return $default;
        }

        $fileContent = $this->fileManager->getContents($filePath);

        return Json::decode($fileContent);
    }

    
    public function saveCustom(string $key1, string $key2, $data): void
    {
        if (is_object($data)) {
            foreach (get_object_vars($data) as $key => $item) {
                if (
                    $item instanceof stdClass &&
                    count(get_object_vars($data)) === 0
                ) {
                    unset($data->$key);
                }
            }
        }

        $filePath = $this->customPath . "/$key1/$key2.json";

        $changedData = Json::encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $this->fileManager->putContents($filePath, $changedData);

        $this->init(true);
    }

    
    public function set(string $key1, string $key2, $data): void
    {
        $this->setInternal($key1, $key2, $data);
    }

    
    public function setParam(string $key1, string $key2, string $param, mixed $value): void
    {
        $this->setInternal($key1, $key2, [$param => $value], true);
    }

    
    private function setInternal(string $key1, string $key2, $data, bool $allowEmptyArray = false): void
    {
        if (!$allowEmptyArray && is_array($data)) {
            foreach ($data as $key => $item) {
                if (is_array($item) && empty($item)) {
                    
                    unset($data[$key]);
                }
            }
        }

        $newData = [
            $key1 => [
                $key2 => $data,
            ],
        ];

        
        $mergedChangedData = Util::merge($this->changedData, $newData);
        
        $mergedData = Util::merge($this->getData(), $newData);

        $this->changedData = $mergedChangedData;
        $this->data = $mergedData;

        if (is_array($data)) {
            $this->undelete($key1, $key2, $data);
        }
    }

    
    public function delete(string $key1, string $key2, $unsets = null): void
    {
        if (!is_array($unsets)) {
            $unsets = (array) $unsets;
        }

        switch ($key1) {
            case 'entityDefs':
                
                $fieldDefinitionList = $this->get('fields');

                $unsetList = $unsets;

                foreach ($unsetList as $unsetItem) {
                    if (preg_match('/fields\.([^.]+)/', $unsetItem, $matches) && isset($matches[1])) {
                        $fieldName = $matches[1];
                        $fieldPath = [$key1, $key2, 'fields', $fieldName];

                        
                        $additionalFields = $this->builderHelper->getAdditionalFieldList(
                            $fieldName,
                            $this->get($fieldPath, []),
                            $fieldDefinitionList
                        );

                        if (is_array($additionalFields)) {
                            foreach ($additionalFields as $additionalFieldName => $additionalFieldParams) {
                                $unsets[] = 'fields.' . $additionalFieldName;
                            }
                        }
                    }
                }
                break;
        }

        $normalizedData = [
            '__APPEND__',
        ];

        $metadataUnsetData = [];

        foreach ($unsets as $unsetItem) {
            $normalizedData[] = $unsetItem;
            $metadataUnsetData[] = implode('.', [$key1, $key2, $unsetItem]);
        }

        $unsetData = [
            $key1 => [
                $key2 => $normalizedData
            ]
        ];

        
        $mergedDeletedData = Util::merge($this->deletedData, $unsetData);
        $this->deletedData = $mergedDeletedData;

        
        
        $unsetDeletedData = Util::unsetInArrayByValue('__APPEND__', $this->deletedData, true);
        $this->deletedData = $unsetDeletedData;

        
        $data = Util::unsetInArray($this->getData(), $metadataUnsetData, true);
        $this->data = $data;
    }

    
    private function undelete(string $key1, string $key2, $data): void
    {
        if (isset($this->deletedData[$key1][$key2])) {
            foreach ($this->deletedData[$key1][$key2] as $unsetIndex => $unsetItem) {
                $value = Util::getValueByKey($data, $unsetItem);

                if (isset($value)) {
                    unset($this->deletedData[$key1][$key2][$unsetIndex]);
                }
            }
        }
    }

    
    public function clearChanges(): void
    {
        $this->changedData = [];
        $this->deletedData = [];

        $this->init(true);
    }

    
    public function save(): bool
    {
        $path = $this->customPath;

        $result = true;

        if (!empty($this->changedData)) {
            foreach ($this->changedData as $key1 => $keyData) {
                foreach ($keyData as $key2 => $data) {
                    if (empty($data)) {
                        continue;
                    }

                    $filePath = $path . "/$key1/$key2.json";

                    $result &= $this->fileManager->mergeJsonContents($filePath, $data);
                }
            }
        }

        if (!empty($this->deletedData)) {
            foreach ($this->deletedData as $key1 => $keyData) {
                foreach ($keyData as $key2 => $unsetData) {
                    if (empty($unsetData)) {
                        continue;
                    }

                    $filePath = $path . "/$key1/$key2.json";

                    $rowResult = $this->fileManager->unsetJsonContents($filePath, $unsetData);

                    if (!$rowResult) {
                        throw new LogicException(
                            "Metadata items $key1.$key2 can be deleted for custom code only."
                        );
                    }
                }
            }
        }

        if (!$result) {
            throw new RuntimeException("Error while saving metadata. See log file for details.");
        }

        $this->clearChanges();

        return (bool) $result;
    }

    
    public function getModuleList(): array
    {
        return $this->module->getOrderedList();
    }

    
    public function getScopeModuleName(string $scopeName): ?string
    {
        return $this->get(['scopes', $scopeName, 'module']);
    }

    private function clearVars(): void
    {
        $this->data = null;
    }
}
