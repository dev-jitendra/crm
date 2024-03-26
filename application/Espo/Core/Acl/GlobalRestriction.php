<?php


namespace Espo\Core\Acl;

use Espo\Core\Utils\Config;
use Espo\Core\Utils\DataCache;
use Espo\Core\Utils\FieldUtil;
use Espo\Core\Utils\Metadata;

use stdClass;


class GlobalRestriction
{
    
    public const TYPE_FORBIDDEN = 'forbidden';
    
    public const TYPE_INTERNAL = 'internal';
    
    public const TYPE_ONLY_ADMIN = 'onlyAdmin';
    
    public const TYPE_READ_ONLY = 'readOnly';
    
    public const TYPE_NON_ADMIN_READ_ONLY = 'nonAdminReadOnly';

    
    private $fieldTypeList = [
        self::TYPE_FORBIDDEN,
        self::TYPE_INTERNAL,
        self::TYPE_ONLY_ADMIN,
        self::TYPE_READ_ONLY,
        self::TYPE_NON_ADMIN_READ_ONLY,
    ];

    
    private $linkTypeList = [
        self::TYPE_FORBIDDEN,
        self::TYPE_INTERNAL,
        self::TYPE_ONLY_ADMIN,
        self::TYPE_READ_ONLY,
        self::TYPE_NON_ADMIN_READ_ONLY,
    ];

    
    private array $entityDefsTypeList = [
        self::TYPE_READ_ONLY,
    ];

    private ?stdClass $data = null;

    private string $cacheKey = 'entityAcl';

    public function __construct(
        private Metadata $metadata,
        private DataCache $dataCache,
        private FieldUtil $fieldUtil,
        Config $config
    ) {

        $useCache = $config->get('useCache');

        if ($useCache && $this->dataCache->has($this->cacheKey)) {
            
            $cachedData = $this->dataCache->get($this->cacheKey);

            $this->data = $cachedData;

            return;
        }

        if (!$this->data) {
            $this->buildData();
        }

        if ($useCache) {
            $this->storeCacheFile();
        }
    }

    private function storeCacheFile(): void
    {
        assert($this->data !== null);

        $this->dataCache->store($this->cacheKey, $this->data);
    }

    private function buildData(): void
    {
        
        $scopeList = array_keys($this->metadata->get(['entityDefs']) ?? []);

        $data = (object) [];

        foreach ($scopeList as $scope) {
            
            $fieldList = array_keys($this->metadata->get(['entityDefs', $scope, 'fields']) ?? []);
            
            $linkList = array_keys($this->metadata->get(['entityDefs', $scope, 'links']) ?? []);

            $isNotEmpty = false;

            $scopeData = (object) [
                'fields' => (object) [],
                'attributes' => (object) [],
                'links' => (object) [],
            ];

            foreach ($this->fieldTypeList as $type) {
                $resultFieldList = [];
                $resultAttributeList = [];

                foreach ($fieldList as $field) {
                    $value = $this->metadata->get(['entityAcl', $scope, 'fields', $field, $type]);

                    if (!$value && in_array($type, $this->entityDefsTypeList)) {
                        $value = $this->metadata->get(['entityDefs', $scope, 'fields', $field, $type]);
                    }

                    if (!$value) {
                        continue;
                    }

                    $isNotEmpty = true;

                    $resultFieldList[] = $field;

                    $fieldAttributeList = $this->fieldUtil->getAttributeList($scope, $field);

                    foreach ($fieldAttributeList as $attribute) {
                        $resultAttributeList[] = $attribute;
                    }
                }

                $scopeData->fields->$type = $resultFieldList;
                $scopeData->attributes->$type = $resultAttributeList;
            }

            foreach ($this->linkTypeList as $type) {
                $resultLinkList = [];

                foreach ($linkList as $link) {
                    $value = $this->metadata->get(['entityAcl', $scope, 'links', $link, $type]);

                    if (!$value && in_array($type, $this->entityDefsTypeList)) {
                        $value = $this->metadata->get(['entityDefs', $scope, 'links', $link, $type]);
                    }

                    if (!$value) {
                        continue;
                    }

                    $isNotEmpty = true;

                    $resultLinkList[] = $link;
                }

                $scopeData->links->$type = $resultLinkList;
            }

            if ($isNotEmpty) {
                $data->$scope = $scopeData;
            }
        }

        $this->data = $data;
    }

    
    public function getScopeRestrictedFieldList(string $scope, string $type): array
    {
        assert($this->data !== null);

        if (!property_exists($this->data, $scope)) {
            return [];
        }

        if (!property_exists($this->data->$scope, 'fields')) {
            return [];
        }

        if (!property_exists($this->data->$scope->fields, $type)) {
            return [];
        }

        return $this->data->$scope->fields->$type;
    }

    
    public function getScopeRestrictedAttributeList(string $scope, string $type): array
    {
        assert($this->data !== null);

        if (!property_exists($this->data, $scope)) {
            return [];
        }

        if (!property_exists($this->data->$scope, 'attributes')) {
            return [];
        }

        if (!property_exists($this->data->$scope->attributes, $type)) {
            return [];
        }

        return $this->data->$scope->attributes->$type;
    }

    
    public function getScopeRestrictedLinkList(string $scope, string $type): array
    {
        assert($this->data !== null);

        if (!property_exists($this->data, $scope)) {
            return [];
        }

        if (!property_exists($this->data->$scope, 'links')) {
            return [];
        }

        if (!property_exists($this->data->$scope->links, $type)) {
            return [];
        }

        return $this->data->$scope->links->$type;
    }
}
