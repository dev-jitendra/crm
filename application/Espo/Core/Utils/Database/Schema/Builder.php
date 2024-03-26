<?php


namespace Espo\Core\Utils\Database\Schema;

use Espo\Core\InjectableFactory;
use Espo\Core\Utils\Database\ConfigDataProvider;
use Espo\Core\Utils\Database\MetadataProvider as MetadataProvider;
use Espo\Core\Utils\File\Manager as FileManager;
use Espo\Core\Utils\Log;
use Espo\Core\Utils\Metadata;
use Espo\Core\Utils\Module\PathProvider;
use Espo\Core\Utils\Util;

use Espo\ORM\Defs\AttributeDefs;
use Espo\ORM\Defs\EntityDefs;
use Espo\ORM\Defs\IndexDefs;
use Espo\ORM\Defs\RelationDefs;
use Espo\ORM\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\DBAL\Schema\SchemaException;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Schema\Schema as DbalSchema;
use Doctrine\DBAL\Types\Type as DbalType;
use Espo\ORM\Type\AttributeType;

use const E_USER_DEPRECATED;


class Builder
{
    private const ATTR_ID = 'id';
    private const ATTR_DELETED = 'deleted';

    private int $idLength;
    private string $idDbType;

    private string $tablesPath = 'Core/Utils/Database/Schema/tables';
    
    private $typeList;
    private ColumnPreparator $columnPreparator;

    public function __construct(
        private Metadata $metadata,
        private FileManager $fileManager,
        private Log $log,
        private InjectableFactory $injectableFactory,
        private PathProvider $pathProvider,
        ConfigDataProvider $configDataProvider,
        ColumnPreparatorFactory $columnPreparatorFactory,
        MetadataProvider $metadataProvider
    ) {
        $this->typeList = array_keys(DbalType::getTypesMap());

        $platform = $configDataProvider->getPlatform();

        $this->columnPreparator = $columnPreparatorFactory->create($platform);

        $this->idLength = $metadataProvider->getIdLength();
        $this->idDbType = $metadataProvider->getIdDbType();
    }

    
    public function build(array $ormMeta, ?array $entityTypeList = null): DbalSchema
    {
        $this->log->debug('Schema\Builder - Start');

        $ormMeta = $this->amendMetadata($ormMeta, $entityTypeList);
        $tables = [];

        $schema = new DbalSchema();

        foreach ($ormMeta as $entityType => $entityParams) {
            $entityDefs = EntityDefs::fromRaw($entityParams, $entityType);

            $this->buildEntity($entityDefs, $schema, $tables);
        }

        foreach ($ormMeta as $entityType => $entityParams) {
            foreach (($entityParams['relations'] ?? []) as $relationName => $relationParams) {
                $relationDefs = RelationDefs::fromRaw($relationParams, $relationName);

                if ($relationDefs->getType() !== Entity::MANY_MANY) {
                    continue;
                }

                $this->buildManyMany($entityType, $relationDefs, $schema, $tables);
            }
        }

        $this->log->debug('Schema\Builder - End');

        return $schema;
    }

    
    private function buildEntity(EntityDefs $entityDefs, DbalSchema $schema, array &$tables): void
    {
        if ($entityDefs->getParam('skipRebuild')) {
            return;
        }

        $entityType = $entityDefs->getName();

        $modifier = $this->getEntityDefsModifier($entityDefs);

        if ($modifier) {
            $modifiedEntityDefs = $modifier->modify($entityDefs);

            $entityDefs = EntityDefs::fromRaw($modifiedEntityDefs->toAssoc(), $entityType);
        }

        $this->log->debug("Schema\Builder: Entity {$entityType}");

        $tableName = Util::toUnderScore($entityType);

        if ($schema->hasTable($tableName)) {
            $tables[$entityType] ??= $schema->getTable($tableName);

            $this->log->debug('Schema\Builder: Table [' . $tableName . '] exists.');

            return;
        }

        $table = $schema->createTable($tableName);

        $tables[$entityType] = $table;

        
        $tableParams = $entityDefs->getParam('params') ?? [];

        foreach ($tableParams as $paramName => $paramValue) {
            $table->addOption($paramName, $paramValue);
        }

        $primaryColumns = [];

        foreach ($entityDefs->getAttributeList() as $attributeDefs) {
            if (
                $attributeDefs->isNotStorable() ||
                $attributeDefs->getType() === Entity::FOREIGN
            ) {
                continue;
            }

            $column = $this->columnPreparator->prepare($attributeDefs);

            if ($attributeDefs->getType() === Entity::ID) {
                $primaryColumns[] = $column->getName();
            }

            if (!in_array($column->getType(), $this->typeList)) {
                $this->log->warning(
                    'Schema\Builder: Column type [' . $column->getType() . '] not supported, ' .
                    $entityType . ':' . $attributeDefs->getName()
                );

                continue;
            }

            if ($table->hasColumn($column->getName())) {
                continue;
            }

            $this->addColumn($table, $column);
        }

        $table->setPrimaryKey($primaryColumns);

        $this->addIndexes($table, $entityDefs->getIndexList());
    }

    private function getEntityDefsModifier(EntityDefs $entityDefs): ?EntityDefsModifier
    {
        
        $modifierClassName = $entityDefs->getParam('modifierClassName');

        if (!$modifierClassName) {
            return null;
        }

        return $this->injectableFactory->create($modifierClassName);
    }

    
    private function amendMetadata(array $ormMeta, ?array $entityTypeList): array
    {
        
        $ormMeta = Util::merge(
            $ormMeta,
            $this->getCustomTables()
        );

        if (isset($ormMeta['unsetIgnore'])) {
            $protectedOrmMeta = [];

            foreach ($ormMeta['unsetIgnore'] as $protectedKey) {
                $protectedOrmMeta = Util::merge(
                    $protectedOrmMeta,
                    Util::fillArrayKeys($protectedKey, Util::getValueByKey($ormMeta, $protectedKey))
                );
            }

            unset($ormMeta['unsetIgnore']);
        }

        
        if (isset($ormMeta['unset'])) {
            
            $ormMeta = Util::unsetInArray($ormMeta, $ormMeta['unset']);

            unset($ormMeta['unset']);
        }

        if (isset($protectedOrmMeta)) {
            
            $ormMeta = Util::merge($ormMeta, $protectedOrmMeta);
        }

        if (isset($entityTypeList)) {
            $dependentEntityTypeList = $this->getDependentEntityTypeList($entityTypeList, $ormMeta);

            $this->log->debug(
                'Schema\Builder: Rebuild for entity types: [' .
                implode(', ', $entityTypeList) . '] with dependent entity types: [' .
                implode(', ', $dependentEntityTypeList) . ']'
            );

            $ormMeta = array_intersect_key($ormMeta, array_flip($dependentEntityTypeList));
        }

        return $ormMeta;
    }

    
    private function addColumn(Table $table, Column $column): void
    {
        $table->addColumn(
            $column->getName(),
            $column->getType(),
            self::convertColumn($column)
        );
    }

    
    private function buildManyMany(
        string $entityType,
        RelationDefs $relationDefs,
        DbalSchema $schema,
        array &$tables
    ): void {

        $relationshipName = $relationDefs->getRelationshipName();

        if (isset($tables[$relationshipName])) {
            return;
        }

        $tableName = Util::toUnderScore($relationshipName);

        $this->log->debug("Schema\Builder: ManyMany for {$entityType}.{$relationDefs->getName()}");

        if ($schema->hasTable($tableName)) {
            $this->log->debug('Schema\Builder: Table [' . $tableName . '] exists.');

            $tables[$relationshipName] ??= $schema->getTable($tableName);

            return;
        }

        $table = $schema->createTable($tableName);

        $idColumn = $this->columnPreparator->prepare(
            AttributeDefs::fromRaw([
                'dbType' => Types::BIGINT,
                'type' => Entity::ID,
                'len' => 20,
                'autoincrement' => true,
            ], self::ATTR_ID)
        );

        $this->addColumn($table, $idColumn);

        if (!$relationDefs->hasMidKey() || !$relationDefs->getForeignMidKey()) {
            $this->log->error('Schema\Builder: Relationship midKeys are empty.', [
                'entityType' => $entityType,
                'relationName' => $relationDefs->getName(),
            ]);

            return;
        }

        $midKeys = [
            $relationDefs->getMidKey(),
            $relationDefs->getForeignMidKey(),
        ];

        foreach ($midKeys as $midKey) {
            $column = $this->columnPreparator->prepare(
                AttributeDefs::fromRaw([
                    'type' => Entity::FOREIGN_ID,
                    'dbType' => $this->idDbType,
                    'len' => $this->idLength,
                ], $midKey)
            );

            $this->addColumn($table, $column);
        }

        
        $additionalColumns = $relationDefs->getParam('additionalColumns') ?? [];

        foreach ($additionalColumns as $fieldName => $fieldParams) {
            if ($fieldParams['type'] === AttributeType::FOREIGN_ID) {
                $fieldParams = array_merge([
                    'dbType' => $this->idDbType,
                    'len' => $this->idLength,
                ], $fieldParams);
            }

            $column = $this->columnPreparator->prepare(AttributeDefs::fromRaw($fieldParams, $fieldName));

            $this->addColumn($table, $column);
        }

        $deletedColumn = $this->columnPreparator->prepare(
            AttributeDefs::fromRaw([
                'type' => Entity::BOOL,
                'default' => false,
            ], self::ATTR_DELETED)
        );

        $this->addColumn($table, $deletedColumn);

        $table->setPrimaryKey([self::ATTR_ID]);

        $this->addIndexes($table, $relationDefs->getIndexList());

        $tables[$relationshipName] = $table;
    }

    
    private function addIndexes(Table $table, array $indexDefsList): void
    {
        foreach ($indexDefsList as $indexDefs) {
            $columns = array_map(
                fn($item) => Util::toUnderScore($item),
                $indexDefs->getColumnList()
            );

            if ($indexDefs->isUnique()) {
                $table->addUniqueIndex($columns, $indexDefs->getKey());

                continue;
            }

            $table->addIndex($columns, $indexDefs->getKey(), $indexDefs->getFlagList());
        }
    }

    
    private static function convertColumn(Column $column): array
    {
        $result = [
            'notnull' => $column->isNotNull(),
        ];

        if ($column->getLength() !== null) {
            $result['length'] = $column->getLength();
        }

        if ($column->getDefault() !== null) {
            $result['default'] = $column->getDefault();
        }

        if ($column->getAutoincrement() !== null) {
            $result['autoincrement'] = $column->getAutoincrement();
        }

        if ($column->getPrecision() !== null) {
            $result['precision'] = $column->getPrecision();
        }

        if ($column->getScale() !== null) {
            $result['scale'] = $column->getScale();
        }

        if ($column->getUnsigned() !== null) {
            $result['unsigned'] = $column->getUnsigned();
        }

        if ($column->getFixed() !== null) {
            $result['fixed'] = $column->getFixed();
        }

        
        $result['platformOptions'] = [];

        if ($column->getCollation()) {
            $result['platformOptions']['collation'] = $column->getCollation();
        }

        if ($column->getCharset()) {
            $result['platformOptions']['charset'] = $column->getCharset();
        }

        return $result;
    }

    
    private function getCustomTables(): array
    {
        $customTables = $this->loadData($this->pathProvider->getCore() . $this->tablesPath);

        foreach ($this->metadata->getModuleList() as $moduleName) {
            $modulePath = $this->pathProvider->getModule($moduleName) . $this->tablesPath;

            $customTables = Util::merge(
                $customTables,
                $this->loadData($modulePath)
            );
        }

        
        $customTables = Util::merge(
            $customTables,
            $this->loadData($this->pathProvider->getCustom() . $this->tablesPath)
        );

        if ($customTables !== []) {
            trigger_error(
                'Definitions in Database\\Schema\\tables are deprecated and will be remove in v8.0.',
                E_USER_DEPRECATED
            );
        }

        return $customTables;
    }

    
    private function getDependentEntityTypeList(array $entityTypeList, array $ormMeta, array $depList = []): array
    {
        foreach ($entityTypeList as $entityType) {
            if (in_array($entityType, $depList)) {
                continue;
            }

            $depList[] = $entityType;

            $entityDefs = EntityDefs::fromRaw($ormMeta[$entityType] ?? [], $entityType);

            foreach ($entityDefs->getRelationList() as $relationDefs) {
                if (!$relationDefs->hasForeignEntityType()) {
                    continue;
                }

                $itemEntityType = $relationDefs->getForeignEntityType();

                if (in_array($itemEntityType, $depList)) {
                    continue;
                }

                $depList = $this->getDependentEntityTypeList([$itemEntityType], $ormMeta, $depList);
            }
        }

        return $depList;
    }

    
    private function loadData(string $path): array
    {
        $tables = [];

        if (!file_exists($path)) {
            return $tables;
        }

        
        $fileList = $this->fileManager->getFileList($path, false, '\.php$', true);

        foreach ($fileList as $fileName) {
            $itemPath = $path . '/' . $fileName;

            if (!$this->fileManager->isFile($itemPath)) {
                continue;
            }

            $fileData = $this->fileManager->getPhpContents($itemPath);

            if (!is_array($fileData)) {
                continue;
            }

            
            $tables = Util::merge($tables, $fileData);
        }

        return $tables;
    }
}
