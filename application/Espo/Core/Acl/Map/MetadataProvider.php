<?php


namespace Espo\Core\Acl\Map;

use Espo\Core\Utils\Metadata;

class MetadataProvider
{
    protected string $type = 'acl';

    public function __construct(private Metadata $metadata)
    {}

    
    public function getScopeList(): array
    {
        
        return array_keys($this->metadata->get('scopes') ?? []);
    }

    public function isScopeEntity(string $scope): bool
    {
        return (bool) $this->metadata->get(['scopes', $scope, 'entity']);
    }

    
    public function getScopeFieldList(string $scope): array
    {
        
        return array_keys($this->metadata->get(['entityDefs', $scope, 'fields']) ?? []);
    }

    
    public function getPermissionList(): array
    {
        $itemList = $this->metadata->get(['app', $this->type, 'valuePermissionList']) ?? [];

        return array_map(
            function (string $item): string {
                if (str_ends_with($item, 'Permission')) {
                    return substr($item, 0, -10);
                }

                return $item;
            },
            $itemList
        );
    }
}
