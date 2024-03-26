<?php


namespace Espo\Core\Acl\Map;

use Espo\Core\Acl\Table;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\DataCache;
use Espo\Core\Utils\ObjectUtil;

use stdClass;
use RuntimeException;


class Map
{
    private stdClass $data;
    private string $cacheKey;
    
    private $forbiddenFieldsCache = [];
    
    private $forbiddenAttributesCache;
    
    private $fieldLevelList = [
        Table::LEVEL_YES,
        Table::LEVEL_NO,
    ];

    public function __construct(
        Table $table,
        private DataBuilder $dataBuilder,
        private Config $config,
        private DataCache $dataCache,
        CacheKeyProvider $cacheKeyProvider
    ) {

        $this->cacheKey = $cacheKeyProvider->get();

        if ($this->config->get('useCache') && $this->dataCache->has($this->cacheKey)) {
            
            $cachedData = $this->dataCache->get($this->cacheKey);

            $this->data = $cachedData;
        }
        else {
            $this->data = $this->dataBuilder->build($table);

            if ($this->config->get('useCache')) {
                $this->dataCache->store($this->cacheKey, $this->data);
            }
        }
    }

    
    public function getData(): stdClass
    {
        return ObjectUtil::clone($this->data);
    }

    
    public function getScopeForbiddenAttributeList(
        string $scope,
        string $action = Table::ACTION_READ,
        string $thresholdLevel = Table::LEVEL_NO
    ): array {

        if (
            !in_array($thresholdLevel, $this->fieldLevelList) ||
            $thresholdLevel === Table::LEVEL_YES
        ) {
            throw new RuntimeException("Bad threshold level.");
        }

        $key = $scope . '_'. $action . '_' . $thresholdLevel;

        if (isset($this->forbiddenAttributesCache[$key])) {
            return $this->forbiddenAttributesCache[$key];
        }

        $fieldTableQuickAccess = $this->data->fieldTableQuickAccess;

        if (
            !isset($fieldTableQuickAccess->$scope) ||
            !isset($fieldTableQuickAccess->$scope->attributes) ||
            !isset($fieldTableQuickAccess->$scope->attributes->$action)
        ) {
            $this->forbiddenAttributesCache[$key] = [];

            return [];
        }

        $levelList = [];

        foreach ($this->fieldLevelList as $level) {
            if (
                array_search($level, $this->fieldLevelList) >=
                array_search($thresholdLevel, $this->fieldLevelList)
            ) {
                $levelList[] = $level;
            }
        }

        $attributeList = [];

        foreach ($levelList as $level) {
            if (!isset($fieldTableQuickAccess->$scope->attributes->$action->$level)) {
                continue;
            }

            foreach ($fieldTableQuickAccess->$scope->attributes->$action->$level as $attribute) {
                if (in_array($attribute, $attributeList)) {
                    continue;
                }

                $attributeList[] = $attribute;
            }
        }

        $this->forbiddenAttributesCache[$key] = $attributeList;

        return $attributeList;
    }

    
    public function getScopeForbiddenFieldList(
        string $scope,
        string $action = Table::ACTION_READ,
        string $thresholdLevel = Table::LEVEL_NO
    ): array {

        if (
            !in_array($thresholdLevel, $this->fieldLevelList) ||
            $thresholdLevel === Table::LEVEL_YES
        ) {
            throw new RuntimeException("Bad threshold level.");
        }

        $key = $scope . '_'. $action . '_' . $thresholdLevel;

        if (isset($this->forbiddenFieldsCache[$key])) {
            return $this->forbiddenFieldsCache[$key];
        }

        $fieldTableQuickAccess = $this->data->fieldTableQuickAccess;

        if (
            !isset($fieldTableQuickAccess->$scope) ||
            !isset($fieldTableQuickAccess->$scope->fields) ||
            !isset($fieldTableQuickAccess->$scope->fields->$action)
        ) {
            $this->forbiddenFieldsCache[$key] = [];

            return [];
        }

        $levelList = [];

        foreach ($this->fieldLevelList as $level) {
            if (
                array_search($level, $this->fieldLevelList) >=
                array_search($thresholdLevel, $this->fieldLevelList)
            ) {
                $levelList[] = $level;
            }
        }

        $fieldList = [];

        foreach ($levelList as $level) {
            if (!isset($fieldTableQuickAccess->$scope->fields->$action->$level)) {
                continue;
            }

            foreach ($fieldTableQuickAccess->$scope->fields->$action->$level as $field) {
                if (in_array($field, $fieldList)) {
                    continue;
                }

                $fieldList[] = $field;
            }
        }

        $this->forbiddenFieldsCache[$key] = $fieldList;

        return $fieldList;
    }
}
