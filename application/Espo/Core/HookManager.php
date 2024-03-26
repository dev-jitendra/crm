<?php


namespace Espo\Core;

use Espo\Core\Hook\GeneralInvoker;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\DataCache;
use Espo\Core\Utils\File\Manager as FileManager;
use Espo\Core\Utils\Log;
use Espo\Core\Utils\Metadata;
use Espo\Core\Utils\Module\PathProvider;
use Espo\Core\Utils\Util;


class HookManager
{
    private const DEFAULT_ORDER = 9;

    
    private $data = null;
    private bool $isDisabled = false;
    
    private $hookListHash = [];
    
    private $hooks;
    private string $cacheKey = 'hooks';
    
    private $ignoredMethodList = [
        '__construct',
        'getDependencyList',
        'inject',
    ];

    public function __construct(
        private InjectableFactory $injectableFactory,
        private FileManager $fileManager,
        private Metadata $metadata,
        private Config $config,
        private DataCache $dataCache,
        private Log $log,
        private PathProvider $pathProvider,
        private GeneralInvoker $generalInvoker
    ) {}

    
    public function process(
        string $scope,
        string $hookName,
        mixed $injection = null,
        array $options = [],
        array $hookData = []
    ): void {

        if ($this->isDisabled) {
            return;
        }

        if (!isset($this->data)) {
            $this->loadHooks();
        }

        $hookList = $this->getHookList($scope, $hookName);

        if (empty($hookList)) {
            return;
        }

        foreach ($hookList as $className) {
            if (empty($this->hooks[$className])) {
                $this->hooks[$className] = $this->createHookByClassName($className);
            }

            $hook = $this->hooks[$className];

            $this->generalInvoker->invoke(
                $hook,
                $hookName,
                $injection,
                $options,
                $hookData
            );
        }
    }

    
    public function disable(): void
    {
        $this->isDisabled = true;
    }

    
    public function enable(): void
    {
        $this->isDisabled = false;
    }

    private function loadHooks(): void
    {
        if ($this->config->get('useCache') && $this->dataCache->has($this->cacheKey)) {
            
            $cachedData = $this->dataCache->get($this->cacheKey);

            $this->data = $cachedData;

            return;
        }

        $metadata = $this->metadata;

        $data = $this->readHookData($this->pathProvider->getCustom() . 'Hooks');

        foreach ($metadata->getModuleList() as $moduleName) {
            $modulePath = $this->pathProvider->getModule($moduleName) . 'Hooks';

            $data = $this->readHookData($modulePath, $data);
        }

        $data = $this->readHookData($this->pathProvider->getCore() . 'Hooks', $data);

        $this->data = $this->sortHooks($data);

        if ($this->config->get('useCache')) {
            $this->dataCache->store($this->cacheKey, $this->data);
        }
    }

    
    private function createHookByClassName(string $className): object
    {
        if (!class_exists($className)) {
            $this->log->error("Hook class '{$className}' does not exist.");
        }

        $obj = $this->injectableFactory->create($className);

        return $obj;
    }

    
    private function readHookData(string $hookDir, array $hookData = []): array
    {
        if (!$this->fileManager->exists($hookDir)) {
            return $hookData;
        }

        
        $fileList = $this->fileManager->getFileList($hookDir, 1, '\.php$', true);

        foreach ($fileList as $scopeName => $hookFiles) {
            $hookScopeDirPath = Util::concatPath($hookDir, $scopeName);
            $normalizedScopeName = Util::normalizeScopeName($scopeName);

            foreach ($hookFiles as $hookFile) {
                $hookFilePath = Util::concatPath($hookScopeDirPath, $hookFile);
                $className = Util::getClassName($hookFilePath);

                $classMethods = get_class_methods($className);

                $hookMethods = array_diff($classMethods, $this->ignoredMethodList);

                
                $hookMethods = array_filter($hookMethods, function ($item) {
                    if (str_starts_with($item, 'set')) {
                        return false;
                    }

                    return true;
                });

                foreach ($hookMethods as $hookType) {
                    $entityHookData = $hookData[$normalizedScopeName][$hookType] ?? [];

                    if ($this->hookExists($className, $entityHookData)) {
                        continue;
                    }

                    if ($this->hookClassIsSuppressed($className)) {
                        continue;
                    }

                    $hookData[$normalizedScopeName][$hookType][] = [
                        'className' => $className,
                        'order' => $className::$order ?? self::DEFAULT_ORDER,
                    ];
                }
            }
        }

        return $hookData;
    }

    
    private function hookClassIsSuppressed(string $className): bool
    {
        $suppressList = $this->metadata->get(['app', 'hook', 'suppressClassNameList']) ?? [];

        return in_array($className, $suppressList);
    }

    
    private function sortHooks(array $hooks): array
    {
        foreach ($hooks as &$scopeHooks) {
            foreach ($scopeHooks as &$hookList) {
                usort($hookList, [$this, 'cmpHooks']);
            }
        }

        return $hooks;
    }

    
    private function getHookList(string $scope, string $hookName): array
    {
        $key = $scope . '_' . $hookName;

        if (!isset($this->hookListHash[$key])) {
            $hookList = [];

            if (isset($this->data['Common'][$hookName])) {
                $hookList = $this->data['Common'][$hookName];
            }

            if (isset($this->data[$scope][$hookName])) {
                $hookList = array_merge($hookList, $this->data[$scope][$hookName]);

                usort($hookList, array($this, 'cmpHooks'));
            }

            $normalizedList = [];

            foreach ($hookList as $hookData) {
                $normalizedList[] = $hookData['className'];
            }

            $this->hookListHash[$key] = $normalizedList;
        }

        return $this->hookListHash[$key];
    }

    
    private function hookExists(string $className, array $hookData): bool
    {
        $class = preg_replace('/^.*\\\(.*)$/', '$1', $className);

        foreach ($hookData as $item) {
            if (preg_match('/\\\\'.$class.'$/', $item['className'])) {
                return true;
            }
        }

        return false;
    }

    
    private function cmpHooks($a, $b): int
    {
        if ($a['order'] == $b['order']) {
            return 0;
        }

        return ($a['order'] < $b['order']) ? -1 : 1;
    }
}
