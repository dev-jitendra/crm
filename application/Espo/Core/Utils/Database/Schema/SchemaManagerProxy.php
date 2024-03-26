<?php


namespace Espo\Core\Utils\Database\Schema;

use Espo\Core\InjectableFactory;
use Espo\Core\Utils\Database\Helper;

use Doctrine\DBAL\Schema\SchemaException;

class SchemaManagerProxy
{
    private ?SchemaManager $schemaManager = null;

    public function __construct(private InjectableFactory $injectableFactory) {}

    private function getSchemaManager(): SchemaManager
    {
        $this->schemaManager ??= $this->injectableFactory->create(SchemaManager::class);

        return $this->schemaManager;
    }

    
    public function rebuild(?array $entityTypeList = null, string $mode = RebuildMode::SOFT): bool
    {
        return $this->getSchemaManager()->rebuild($entityTypeList, $mode);
    }

    public function getDatabaseHelper(): Helper
    {
        return $this->getSchemaManager()->getDatabaseHelper();
    }
}
