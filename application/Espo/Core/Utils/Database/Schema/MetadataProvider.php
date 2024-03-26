<?php


namespace Espo\Core\Utils\Database\Schema;

use Doctrine\DBAL\Types\Type;
use Espo\Core\Utils\Database\ConfigDataProvider;
use Espo\Core\Utils\Metadata;

class MetadataProvider
{
    public function __construct(
        private ConfigDataProvider $configDataProvider,
        private Metadata $metadata
    ) {}

    private function getPlatform(): string
    {
        return $this->configDataProvider->getPlatform();
    }

    
    public function getPreRebuildActionClassNameList(): array
    {
        
        return $this->metadata
            ->get(['app', 'databasePlatforms', $this->getPlatform(), 'preRebuildActionClassNameList']) ?? [];
    }

    
    public function getPostRebuildActionClassNameList(): array
    {
        
        return $this->metadata
            ->get(['app', 'databasePlatforms', $this->getPlatform(), 'postRebuildActionClassNameList']) ?? [];
    }

    
    public function getDbalTypeClassNameMap(): array
    {
        
        return $this->metadata
            ->get(['app', 'databasePlatforms', $this->getPlatform(), 'dbalTypeClassNameMap']) ?? [];
    }
}
