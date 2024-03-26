<?php


namespace Espo\Core;

use Espo\Core\Exceptions\Error;
use Espo\Core\ORM\EntityManagerProxy;
use Espo\Core\Utils\Database\Helper as DatabaseHelper;
use Espo\Core\Utils\Database\Schema\RebuildMode;
use Espo\Core\Utils\Database\Schema\SchemaManagerProxy;
use Espo\Core\Utils\File\Manager as FileManager;
use Espo\Core\Utils\Metadata;
use Espo\Core\Utils\Util;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\Config\ConfigWriter;
use Espo\Core\Utils\Metadata\OrmMetadataData;
use Espo\Core\Utils\Log;
use Espo\Core\Utils\Module;
use Espo\Core\Rebuild\RebuildActionProcessor;
use Espo\Core\ORM\DatabaseParamsFactory;
use Espo\Core\Utils\Config\MissingDefaultParamsSaver as ConfigMissingDefaultParamsSaver;

use Throwable;


class DataManager
{
    private string $cachePath = 'data/cache';

    public function __construct(
        private EntityManagerProxy $entityManager,
        private Config $config,
        private ConfigWriter $configWriter,
        private Metadata $metadata,
        private OrmMetadataData $ormMetadataData,
        private HookManager $hookManager,
        private SchemaManagerProxy $schemaManager,
        private Log $log,
        private Module $module,
        private RebuildActionProcessor $rebuildActionProcessor,
        private ConfigMissingDefaultParamsSaver $configMissingDefaultParamsSaver,
        private FileManager $fileManager,
        private DatabaseParamsFactory $databaseParamsFactory,
        private InjectableFactory $injectableFactory
    ) {}

    
    public function rebuild(?array $entityTypeList = null): void
    {
        $this->clearCache();
        $this->disableHooks();
        $this->checkModules();
        $this->rebuildMetadata();
        $this->populateConfigParameters();
        $this->rebuildDatabase($entityTypeList);
        $this->rebuildActionProcessor->process();
        $this->configMissingDefaultParamsSaver->process();
        $this->enableHooks();
    }

    
    public function clearCache(): void
    {
        $this->module->clearCache();

        $result = $this->fileManager->removeInDir($this->cachePath);

        if (!$result) {
            throw new Error("Error while clearing cache");
        }

        $this->updateCacheTimestamp();
    }

    
    public function rebuildDatabase(?array $entityTypeList = null, string $mode = RebuildMode::SOFT): void
    {
        $schemaManager = $this->schemaManager;

        try {
            $result = $schemaManager->rebuild($entityTypeList, $mode);
        }
        catch (Throwable $e) {
            $result = false;

            $this->log->error(
                "Failed to rebuild database schema. Details: ". $e->getMessage() .
                " at " . $e->getFile() . ":" . $e->getLine()
            );
        }

        if (!$result) {
            throw new Error("Error while rebuilding database. See log file for details.");
        }

        $databaseType = strtolower($schemaManager->getDatabaseHelper()->getType());

        if (
            !$this->config->get('actualDatabaseType') ||
            $this->config->get('actualDatabaseType') !== $databaseType
        ) {
            $this->configWriter->set('actualDatabaseType', $databaseType);
        }

        $databaseVersion = $schemaManager->getDatabaseHelper()->getVersion();

        if (
            !$this->config->get('actualDatabaseVersion') ||
            $this->config->get('actualDatabaseVersion') !== $databaseVersion
        ) {
            $this->configWriter->set('actualDatabaseVersion', $databaseVersion);
        }

        $this->configWriter->updateCacheTimestamp();
        $this->configWriter->save();
    }

    
    public function rebuildMetadata(): void
    {
        $this->metadata->init(true);
        $this->ormMetadataData->reload();
        $this->entityManager->getMetadata()->updateData();

        $this->updateCacheTimestamp();
    }

    
    public function updateCacheTimestamp(): void
    {
        $this->configWriter->updateCacheTimestamp();
        $this->configWriter->save();
    }

    
    public function updateAppTimestamp(): void
    {
        $this->configWriter->set('appTimestamp', time());
        $this->configWriter->save();
    }

    private function populateConfigParameters(): void
    {
        $this->setFullTextConfigParameters();
        $this->setCryptKeyConfigParameter();

        if (!$this->config->get('appTimestamp')) {
            $this->updateAppTimestamp();
        }

        $this->configWriter->save();
    }

    private function setFullTextConfigParameters(): void
    {
        $databaseParams = $this->databaseParamsFactory->create();

        if ($databaseParams->getPlatform() !== 'Mysql') {
            return;
        }

        $helper = $this->injectableFactory->create(DatabaseHelper::class);

        $fullTextSearchMinLength = $helper->getParam('ft_min_word_len');

        if ($fullTextSearchMinLength !== null) {
            $fullTextSearchMinLength = (int) $fullTextSearchMinLength;
        }

        $this->configWriter->set('fullTextSearchMinLength', $fullTextSearchMinLength);
    }

    private function setCryptKeyConfigParameter(): void
    {
        if ($this->config->get('cryptKey')) {
            return;
        }

        $cryptKey = Util::generateSecretKey();

        $this->configWriter->set('cryptKey', $cryptKey);
    }

    private function disableHooks(): void
    {
        $this->hookManager->disable();
    }

    private function enableHooks(): void
    {
        $this->hookManager->enable();
    }

    
    private function checkModules(): void
    {
        $moduleNameList = $this->module->getList();

        if (count(array_unique($moduleNameList)) !== count($moduleNameList)) {
            throw new Error(
                "There is a same module in both `custom` and `internal` directories. " .
                "Should be only in one location."
            );
        }
    }
}
