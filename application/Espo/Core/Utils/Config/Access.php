<?php


namespace Espo\Core\Utils\Config;

use Espo\Core\Utils\Config;
use Espo\Core\Utils\Metadata;
use Espo\Core\Utils\FieldUtil;
use Espo\Entities\Settings;

class Access
{
    
    public const LEVEL_DEFAULT = 'default';
    
    public const LEVEL_SYSTEM = 'system';
    
    public const LEVEL_INTERNAL = 'internal';
    
    public const LEVEL_SUPER_ADMIN = 'superAdmin';
    
    public const LEVEL_ADMIN = 'admin';
    
    public const LEVEL_GLOBAL = 'global';

    public function __construct(
        private Config $config,
        private Metadata $metadata,
        private FieldUtil $fieldUtil
    ) {}

    
    public function getReadOnlyParamList(): array
    {
        $itemList = [];

        $fieldDefs = $this->metadata->get(['entityDefs', Settings::ENTITY_TYPE, 'fields']);

        foreach ($fieldDefs as $field => $fieldParams) {
            if (empty($fieldParams['readOnly'])) {
                continue;
            }

            foreach ($this->fieldUtil->getAttributeList(Settings::ENTITY_TYPE, $field) as $attribute) {
                $itemList[] = $attribute;
            }
        }

        $params = $this->metadata->get(['app', 'config', 'params']) ?? [];

        foreach ($params as $name => $item) {
            if ($item['readOnly'] ?? false) {
                $itemList[] = $name;
            }
        }

        return array_values(array_unique($itemList));
    }

    
    public function getAdminParamList(): array
    {
        $itemList = $this->config->get('adminItems') ?? [];

        $fieldDefs = $this->metadata->get(['entityDefs', Settings::ENTITY_TYPE, 'fields']);

        foreach ($fieldDefs as $field => $fieldParams) {
            if (empty($fieldParams['onlyAdmin'])) {
                continue;
            }

            foreach ($this->fieldUtil->getAttributeList(Settings::ENTITY_TYPE, $field) as $attribute) {
                $itemList[] = $attribute;
            }
        }

        return array_values(
            array_merge(
                $itemList,
                $this->getParamListByLevel(self::LEVEL_ADMIN)
            )
        );
    }

    
    public function getInternalParamList(): array
    {
        return $this->getParamListByLevel(self::LEVEL_INTERNAL);
    }

    
    public function getSystemParamList(): array
    {
        $itemList = $this->config->get('systemItems') ?? [];

        $fieldDefs = $this->metadata->get(['entityDefs', Settings::ENTITY_TYPE, 'fields']);

        foreach ($fieldDefs as $field => $fieldParams) {
            if (empty($fieldParams['onlySystem'])) {
                continue;
            }

            foreach ($this->fieldUtil->getAttributeList(Settings::ENTITY_TYPE, $field) as $attribute) {
                $itemList[] = $attribute;
            }
        }

        return array_values(
            array_merge(
                $itemList,
                $this->getParamListByLevel(self::LEVEL_SYSTEM)
            )
        );
    }

    
    public function getGlobalParamList(): array
    {
        $itemList = $this->config->get('globalItems', []);

        $fieldDefs = $this->metadata->get(['entityDefs', Settings::ENTITY_TYPE, 'fields']);

        foreach ($fieldDefs as $field => $fieldParams) {
            if (empty($fieldParams['global'])) {
                continue;
            }

            foreach ($this->fieldUtil->getAttributeList(Settings::ENTITY_TYPE, $field) as $attribute) {
                $itemList[] = $attribute;
            }
        }

        return array_values(
            array_merge(
                $itemList,
                $this->getParamListByLevel(self::LEVEL_GLOBAL)
            )
        );
    }

    
    public function getSuperAdminParamList(): array
    {
        return array_values(
            array_merge(
                $this->config->get('superAdminItems') ?? [],
                $this->getParamListByLevel(self::LEVEL_SUPER_ADMIN)
            )
        );
    }

    
    private function getParamListByLevel(string $level): array
    {
        $itemList = [];

        $params = $this->metadata->get(['app', 'config', 'params']) ?? [];

        foreach ($params as $name => $item) {
            $levelItem = $item['level'] ?? null;

            if ($levelItem !== $level) {
                continue;
            }

            $itemList[] = $name;
        }

        return $itemList;
    }
}
