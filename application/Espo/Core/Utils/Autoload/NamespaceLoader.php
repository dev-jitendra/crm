<?php


namespace Espo\Core\Utils\Autoload;

use Espo\Core\Utils\Config;
use Espo\Core\Utils\DataCache;
use Espo\Core\Utils\File\Manager as FileManager;
use Espo\Core\Utils\Log;
use Espo\Core\Utils\Util;

use Composer\Autoload\ClassLoader;

use Throwable;

class NamespaceLoader
{
    
    private $namespaces = null;
    
    private $vendorNamespaces = null;
    private string $autoloadFilePath = 'vendor/autoload.php';
    
    private $namespacesPaths = [
        'psr-4' => 'vendor/composer/autoload_psr4.php',
        'psr-0' => 'vendor/composer/autoload_namespaces.php',
        'classmap' => 'vendor/composer/autoload_classmap.php',
    ];
    
    private $methodNameMap = [
        'psr-4' => 'addPsr4',
        'psr-0' => 'add',
    ];
    private string $cacheKey = 'autoloadVendorNamespaces';

    private ClassLoader $classLoader;

    public function __construct(
        private Config $config,
        private DataCache $dataCache,
        private FileManager $fileManager,
        private Log $log
    ) {

        $this->classLoader = new ClassLoader();
    }

    
    public function register(array $data): void
    {
        $this->addListToClassLoader($data);

        $this->classLoader->register(true);
    }

    
    private function loadNamespaces(string $basePath = ''): array
    {
        $namespaces = [];

        foreach ($this->namespacesPaths as $type => $path) {
            $mapFile = Util::concatPath($basePath, $path);

            if (!$this->fileManager->exists($mapFile)) {
                continue;
            }

            $map = require($mapFile);

            if (!empty($map) && is_array($map)) {
                $namespaces[$type] = $map;
            }
        }

        return $namespaces;
    }

    
    private function getNamespaces(): array
    {
        if (!$this->namespaces) {
            $this->namespaces = $this->loadNamespaces();
        }

        return $this->namespaces;
    }

    
    private function getNamespaceList(string $type): array
    {
        $namespaces = $this->getNamespaces();

        return array_keys($namespaces[$type] ?? []);
    }

    
    private function addNamespace(string $type, string $name, $path): void
    {
        if (!$this->namespaces) {
            $this->getNamespaces();
        }

        $this->namespaces[$type][$name] = (array) $path;
    }

    
    private function hasNamespace(string $type, string $name): bool
    {
        if (in_array($name, $this->getNamespaceList($type))) {
            return true;
        }

        if (!preg_match('/\\\$/', $name)) {
            $name = $name . '\\';

            if (in_array($name, $this->getNamespaceList($type))) {
                return true;
            }
        }

        return false;
    }

    
    private function addListToClassLoader(array $data, bool $skipVendorNamespaces = false): void
    {
        foreach ($this->methodNameMap as $type => $methodName) {
            $itemData = $data[$type] ?? null;

            if ($itemData === null) {
                continue;
            }

            foreach ($itemData as $prefix => $path) {
                if (!$skipVendorNamespaces) {
                    $vendorPaths = is_array($path) ? $path : (array) $path;

                    foreach ($vendorPaths as $vendorPath) {
                        $this->addListToClassLoader(
                            $this->getVendorNamespaces($vendorPath),
                            true
                        );
                    }
                }

                if ($this->hasNamespace($type, $prefix)) {
                    continue;
                }

                try {
                    $this->classLoader->$methodName($prefix, $path);
                }
                catch (Throwable $e) {
                    $this->log->error("Could not add '{$prefix}' to autoload: " . $e->getMessage());

                    continue;
                }

                $this->addNamespace($type, $prefix, $path);
            }
        }

        $classMap = $data['classmap'] ?? null;

        if ($classMap !== null) {
            $this->classLoader->addClassMap($classMap);
        }
    }

    
    private function getVendorNamespaces(string $path): array
    {
        $useCache = $this->config->get('useCache');

        if (!isset($this->vendorNamespaces)) {
            $this->vendorNamespaces = [];

            if ($useCache && $this->dataCache->has($this->cacheKey)) {
                
                $cachedData = $this->dataCache->get($this->cacheKey);

                $this->vendorNamespaces = $cachedData;
            }
        }

        assert($this->vendorNamespaces !== null);

        if (!array_key_exists($path, $this->vendorNamespaces)) {
            $vendorPath = $this->findVendorPath($path);

            if ($vendorPath) {
                $this->vendorNamespaces[$path] = $this->loadNamespaces($vendorPath);

                if ($useCache) {
                    $this->dataCache->store($this->cacheKey, $this->vendorNamespaces);
                }
            }
        }

        return $this->vendorNamespaces[$path] ?? [];
    }

    private function findVendorPath(string $path): ?string
    {
        $vendor = Util::concatPath($path, $this->autoloadFilePath);

        if ($this->fileManager->exists($vendor)) {
            return $path;
        }

        $parentDir = dirname($path);

        if (!empty($parentDir) && $parentDir !== '.') {
            return $this->findVendorPath($parentDir);
        }

        return null;
    }
}
