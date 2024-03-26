<?php


namespace Espo\Core\Utils\Database\Schema;

use Doctrine\DBAL\Connection as DbalConnection;
use Doctrine\DBAL\Exception as DbalException;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Comparator;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\SchemaDiff;
use Doctrine\DBAL\Schema\SchemaException;
use Doctrine\DBAL\Types\Type;

use Espo\Core\Binding\BindingContainerBuilder;
use Espo\Core\InjectableFactory;
use Espo\Core\Utils\Database\Helper;
use Espo\Core\Utils\Log;
use Espo\Core\Utils\Metadata\OrmMetadataData;

use Throwable;


class SchemaManager
{
    
    private AbstractSchemaManager $schemaManager;
    private Comparator $comparator;
    private Builder $builder;

    
    public function __construct(
        private OrmMetadataData $ormMetadataData,
        private Log $log,
        private Helper $helper,
        private MetadataProvider $metadataProvider,
        private DiffModifier $diffModifier,
        private InjectableFactory $injectableFactory
    ) {
        $this->schemaManager = $this->getDbalConnection()
            ->getDatabasePlatform()
            ->createSchemaManager($this->getDbalConnection());

        
        
        
        $this->comparator = new Comparator($this->getPlatform());

        $this->initFieldTypes();

        $this->builder = $this->injectableFactory->createWithBinding(
            Builder::class,
            BindingContainerBuilder::create()
                ->bindInstance(Helper::class, $this->helper)
                ->build()
        );
    }

    public function getDatabaseHelper(): Helper
    {
        return $this->helper;
    }

    
    private function getPlatform(): AbstractPlatform
    {
        return $this->getDbalConnection()->getDatabasePlatform();
    }

    private function getDbalConnection(): DbalConnection
    {
        return $this->helper->getDbalConnection();
    }

    
    private function initFieldTypes(): void
    {
        foreach ($this->metadataProvider->getDbalTypeClassNameMap() as $type => $className) {
            Type::hasType($type) ?
                Type::overrideType($type, $className) :
                Type::addType($type, $className);

            $this->getDbalConnection()
                ->getDatabasePlatform()
                ->registerDoctrineTypeMapping($type, $type);
        }
    }

    
    public function rebuild(?array $entityTypeList = null, string $mode = RebuildMode::SOFT): bool
    {
        $fromSchema = $this->introspectSchema();
        $schema = $this->builder->build($this->ormMetadataData->getData(), $entityTypeList);

        try {
            $this->processPreRebuildActions($fromSchema, $schema);
        }
        catch (Throwable $e) {
            $this->log->alert('Rebuild database pre-rebuild error: '. $e->getMessage());

            return false;
        }

        $diff = $this->comparator->compareSchemas($fromSchema, $schema);
        $needReRun = $this->diffModifier->modify($diff, $schema, false, $mode);
        $sql = $this->composeDiffSql($diff);

        $result = $this->runSql($sql);

        if (!$result) {
            return false;
        }

        if ($needReRun) {
            
            
            
            $intermediateSchema = $this->introspectSchema();
            $schema = $this->builder->build($this->ormMetadataData->getData(), $entityTypeList);

            $diff = $this->comparator->compareSchemas($intermediateSchema, $schema);

            $this->diffModifier->modify($diff, $schema, true);
            $sql = $this->composeDiffSql($diff);
            $result = $this->runSql($sql);
        }

        if (!$result) {
            return false;
        }

        try {
            $this->processPostRebuildActions($fromSchema, $schema);
        }
        catch (Throwable $e) {
            $this->log->alert('Rebuild database post-rebuild error: ' . $e->getMessage());

            return false;
        }

        return true;
    }

    
    private function runSql(array $queries): bool
    {
        $result = true;

        $connection = $this->getDbalConnection();

        foreach ($queries as $sql) {
            $this->log->info('Schema, query: '. $sql);

            try {
                $connection->executeQuery($sql);
            }
            catch (Throwable $e) {
                $this->log->alert('Rebuild database error: ' . $e->getMessage());

                $result = false;
            }
        }

        return $result;
    }

    
    private function introspectSchema(): Schema
    {
        return $this->schemaManager->introspectSchema();
    }

    
    private function composeDiffSql(SchemaDiff $diff): array
    {
        return $this->getPlatform()->getAlterSchemaSQL($diff);
    }

    private function processPreRebuildActions(Schema $actualSchema, Schema $schema): void
    {
        $binding = BindingContainerBuilder::create()
            ->bindInstance(Helper::class, $this->helper)
            ->build();

        foreach ($this->metadataProvider->getPreRebuildActionClassNameList() as $className) {
            $action = $this->injectableFactory->createWithBinding($className, $binding);

            $action->process($actualSchema, $schema);
        }
    }

    private function processPostRebuildActions(Schema $actualSchema, Schema $schema): void
    {
        $binding = BindingContainerBuilder::create()
            ->bindInstance(Helper::class, $this->helper)
            ->build();

        foreach ($this->metadataProvider->getPostRebuildActionClassNameList() as $className) {
            $action = $this->injectableFactory->createWithBinding($className, $binding);

            $action->process($actualSchema, $schema);
        }
    }
}
