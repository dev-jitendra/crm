<?php


namespace Espo\Core\Utils\Config;

use Espo\Core\Utils\Config;

use Exception;
use RuntimeException;


class ConfigWriter
{
    
    private $changedData = [];
    
    private $removeParamList = [];
    
    protected $associativeArrayAttributeList = [
        'currencyRates',
        'database',
        'logger',
        'defaultPermissions',
    ];

    private string $cacheTimestampParam = 'cacheTimestamp';

    public function __construct(
        private Config $config,
        private ConfigWriterFileManager $fileManager,
        private ConfigWriterHelper $helper,
        private InternalConfigHelper $internalConfigHelper
    ) {}

    
    public function set(string $name, $value): void
    {
        if (in_array($name, $this->associativeArrayAttributeList) && is_object($value)) {
            $value = (array) $value;
        }

        $this->changedData[$name] = $value;
    }

    
    public function setMultiple(array $params): void
    {
        foreach ($params as $name => $value) {
            $this->set($name, $value);
        }
    }

    
    public function remove(string $name): void
    {
        $this->removeParamList[] = $name;
    }

    
    public function save(): void
    {
        $changedData = $this->changedData;

        if (!isset($changedData[$this->cacheTimestampParam])) {
            $changedData[$this->cacheTimestampParam] = $this->generateCacheTimestamp();
        }

        $configPath = $this->config->getConfigPath();
        $internalConfigPath = $this->config->getInternalConfigPath();

        if (!$this->fileManager->isFile($configPath)) {
            throw new RuntimeException("Config file '{$configPath}' not found.");
        }

        $data = $this->fileManager->getPhpContents($configPath);

        $dataInternal = $this->fileManager->isFile($internalConfigPath) ?
            $this->fileManager->getPhpContents($internalConfigPath) : [];

        if (!is_array($data)) {
            throw new RuntimeException("Could not read config.");
        }

        if (!is_array($dataInternal)) {
            throw new RuntimeException("Could not read config-internal.");
        }

        $toSaveInternal = false;

        foreach ($changedData as $key => $value) {
            if ($this->internalConfigHelper->isParamForInternalConfig($key)) {
                $dataInternal[$key] = $value;
                unset($data[$key]);

                $toSaveInternal = true;

                continue;
            }

            $data[$key] = $value;
        }

        foreach ($this->removeParamList as $key) {
            if ($this->internalConfigHelper->isParamForInternalConfig($key)) {
                unset($dataInternal[$key]);

                $toSaveInternal = true;

                continue;
            }

            unset($data[$key]);
        }

        if ($toSaveInternal) {
            $this->saveData($internalConfigPath, $dataInternal, 'microtimeInternal');
        }

        $this->saveData($configPath, $data, 'microtime');

        $this->changedData = [];
        $this->removeParamList = [];

        $this->config->update();
    }

    
    private function saveData(string $path, array &$data, string $timeParam): void
    {
        $data[$timeParam] = $microtime = $this->helper->generateMicrotime();

        try {
            $this->fileManager->putPhpContents($path, $data);
        }
        catch (Exception $e) {
            throw new RuntimeException("Could not save config.");
        }

        $reloadedData = $this->fileManager->getPhpContents($path);

        if (
            is_array($reloadedData) &&
            $microtime === ($reloadedData[$timeParam] ?? null)
        ) {
            return;
        }

        try {
            $this->fileManager->putPhpContentsNoRenaming($path, $data);
        }
        catch (Exception $e) {
            throw new RuntimeException("Could not save config.");
        }
    }

    
    public function updateCacheTimestamp(): void
    {
        $this->set($this->cacheTimestampParam, $this->generateCacheTimestamp());
    }

    protected function generateCacheTimestamp(): int
    {
        return $this->helper->generateCacheTimestamp();
    }
}
