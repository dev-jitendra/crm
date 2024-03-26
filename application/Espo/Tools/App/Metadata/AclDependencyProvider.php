<?php


namespace Espo\Tools\App\Metadata;

use Espo\Core\Utils\Config;
use Espo\Core\Utils\DataCache;
use Espo\Core\Utils\Metadata;
use Espo\ORM\Defs;

class AclDependencyProvider
{
    private const CACHE_KEY = 'metadataAclDependency';

    
    private array $enumFieldTypeList = [
        'enum',
        'multiEnum',
        'array',
        'checklist',
    ];

    
    private ?array $data = null;
    private bool $useCache;

    public function __construct(
        private DataCache $dataCache,
        private Metadata $metadata,
        Config $config,
        private Defs $ormDefs
    ) {
        $this->useCache = $config->get('useCache');
    }

    
    public function get(): array
    {
        if ($this->data === null) {
            $this->data = $this->loadData();
        }

        return $this->data;
    }

    
    private function loadData(): array
    {
        if ($this->useCache && $this->dataCache->has(self::CACHE_KEY)) {
            
            $raw = $this->dataCache->get(self::CACHE_KEY);

            return $this->buildFromRaw($raw);
        }

        return $this->buildData();
    }

    
    private function buildData(): array
    {
        $data = [];

        foreach (($this->metadata->get(['app', 'metadata', 'aclDependencies']) ?? []) as $target => $item) {
            $scope = $item['scope'] ?? null;
            $field = $item['field'] ?? null;

            if (!$scope) {
                continue;
            }

            $data[] = [
                'target' => $target,
                'scope' => $scope,
                'field' => $field,
            ];
        }

        foreach ($this->ormDefs->getEntityList() as $entityDefs) {
            if (!$this->metadata->get(['scopes', $entityDefs->getName(), 'object'])) {
                continue;
            }

            foreach ($entityDefs->getFieldList() as $fieldDefs) {
                if (!in_array($fieldDefs->getType(), $this->enumFieldTypeList)) {
                    continue;
                }

                $optionsPath = $fieldDefs->getParam('optionsPath');
                $optionsReference = $fieldDefs->getParam('optionsReference');

                if (
                    !$optionsPath &&
                    $optionsReference &&
                    str_contains($optionsReference, '.')
                ) {
                    [$refEntityType, $refField] = explode('.', $optionsReference);

                    $optionsPath = "entityDefs.{$refEntityType}.fields.{$refField}.options";
                }

                if (!$optionsPath) {
                    continue;
                }

                $data[] = [
                    'target' => $optionsPath,
                    'scope' => $entityDefs->getName(),
                    'field' => $fieldDefs->getName(),
                ];
            }
        }

        if ($this->useCache) {
            $this->dataCache->store(self::CACHE_KEY, $data);
        }

        return $this->buildFromRaw($data);
    }

    
    private function buildFromRaw(array $raw): array
    {
        $list = [];

        foreach ($raw as $rawItem) {
            $target = $rawItem['target'] ?? null;
            $scope = $rawItem['scope'] ?? null;
            $field = $rawItem['field'] ?? null;

            $list[] = new AclDependencyItem($target, $scope, $field);
        }

        return $list;
    }
}
