<?php


namespace Espo\Core\Utils;

use Espo\Core\Utils\Config\ConfigFileManager;

use stdClass;
use RuntimeException;

use const E_USER_DEPRECATED;


class Config
{
    private string $configPath = 'data/config.php';
    private string $internalConfigPath = 'data/config-internal.php';
    private string $systemConfigPath = 'application/Espo/Resources/defaults/systemConfig.php';
    private string $cacheTimestamp = 'cacheTimestamp';
    
    protected $associativeArrayAttributeList = [
        'currencyRates',
        'database',
        'logger',
        'defaultPermissions',
    ];
    
    private $data = null;
    
    private $changedData = [];
    
    private $removeData = [];
    
    private $internalParamList = [];

    public function __construct(private ConfigFileManager $fileManager)
    {}

    
    public function getConfigPath(): string
    {
        return $this->configPath;
    }

    
    public function getInternalConfigPath(): string
    {
        return $this->internalConfigPath;
    }

    
    public function get(string $name, $default = null)
    {
        $keys = explode('.', $name);

        $lastBranch = $this->getData();

        foreach ($keys as $key) {
            if (!is_array($lastBranch) && !is_object($lastBranch)) {
                return $default;
            }

            if (is_array($lastBranch) && !array_key_exists($key, $lastBranch)) {
                return $default;
            }

            if (is_object($lastBranch) && !property_exists($lastBranch, $key)) {
                return $default;
            }

            if (is_array($lastBranch)) {
                $lastBranch = $lastBranch[$key];

                continue;
            }

            $lastBranch = $lastBranch->$key;
        }

        return $lastBranch;
    }

    
    public function has(string $name): bool
    {
        $keys = explode('.', $name);

        $lastBranch = $this->getData();

        foreach ($keys as $key) {
            if (!is_array($lastBranch) && !is_object($lastBranch)) {
                return false;
            }

            if (is_array($lastBranch) && !array_key_exists($key, $lastBranch)) {
                return false;
            }

            if (is_object($lastBranch) && !property_exists($lastBranch, $key)) {
                return false;
            }

            if (is_array($lastBranch)) {
                $lastBranch = $lastBranch[$key];

                continue;
            }

            $lastBranch = $lastBranch->$key;
        }

        return true;
    }

    
    public function update(): void
    {
        $this->load();
    }

    
    public function set($name, $value = null, bool $dontMarkDirty = false): void
    {
        if (is_object($name)) {
            $name = get_object_vars($name);
        }

        if (!is_array($name)) {
            $name = [$name => $value];
        }

        foreach ($name as $key => $value) {
            if (in_array($key, $this->associativeArrayAttributeList) && is_object($value)) {
                $value = (array) $value;
            }

            $this->data[$key] = $value;

            if (!$dontMarkDirty) {
                $this->changedData[$key] = $value;
            }
        }
    }

    
    public function remove(string $name): bool
    {
        assert($this->data !== null);

        if (array_key_exists($name, $this->data)) {
            unset($this->data[$name]);

            $this->removeData[] = $name;

            return true;
        }

        return false;
    }

    
    public function save()
    {
        trigger_error(
            "Config::save is deprecated. Use `Espo\Core\Utils\Config\ConfigWriter` to save the config.",
            E_USER_DEPRECATED
        );

        $values = $this->changedData;

        if (!isset($values[$this->cacheTimestamp])) {
            $values = array_merge($this->updateCacheTimestamp(true) ?? [], $values);
        }

        $removeData = empty($this->removeData) ? null : $this->removeData;

        $configPath = $this->getConfigPath();

        if (!$this->fileManager->isFile($configPath)) {
            throw new RuntimeException("Config file '{$configPath}' is not found.");
        }

        $data = include($configPath);

        if (!is_array($data)) {
            $data = include($configPath);
        }

        if (is_array($values)) {
            foreach ($values as $key => $value) {
                $data[$key] = $value;
            }
        }

        if (is_array($removeData)) {
            foreach ($removeData as $key) {
                unset($data[$key]);
            }
        }

        if (!is_array($data)) {
            throw new RuntimeException('Invalid config data while saving.');
        }

        $data['microtime'] = microtime(true);

        $this->fileManager->putPhpContents($configPath, $data);

        $this->changedData = [];
        $this->removeData = [];

        $this->load();

        return true;
    }

    private function isLoaded(): bool
    {
        return isset($this->data) && !empty($this->data);
    }

    
    private function getData(): array
    {
        if (!$this->isLoaded()) {
            $this->load();
        }

        assert($this->data !== null);

        return $this->data;
    }

    private function load(): void
    {
        $systemData = $this->fileManager->getPhpContents($this->systemConfigPath);

        $data = $this->fileManager->isFile($this->configPath) ?
            $this->fileManager->getPhpContents($this->configPath) : [];

        $internalData = $this->fileManager->isFile($this->internalConfigPath) ?
            $this->fileManager->getPhpContents($this->internalConfigPath) : [];

        
        $mergedData = Util::merge(
            Util::merge($systemData, $data),
            $internalData
        );

        $this->data = $mergedData;

        $this->internalParamList = array_keys($internalData);

        $this->fileManager->setConfig($this);
    }

    
    public function getAllNonInternalData(): stdClass
    {
        $data = (object) $this->getData();

        foreach ($this->internalParamList as $param) {
            unset($data->$param);
        }

        return $data;
    }

    
    public function isInternal(string $name): bool
    {
        if (!$this->isLoaded()) {
            $this->load();
        }

        return in_array($name, $this->internalParamList);
    }

    
    public function setData($data)
    {
        if (is_object($data)) {
            $data = get_object_vars($data);
        }

        $this->set($data);
    }

    
    public function updateCacheTimestamp(bool $returnOnlyValue = false)
    {
        $timestamp = [
            $this->cacheTimestamp => time()
        ];

        if ($returnOnlyValue) {
            return $timestamp;
        }

        $this->set($timestamp);

        return null;
    }

    
    public function getSiteUrl(): string
    {
        return rtrim($this->get('siteUrl'), '/');
    }
}
