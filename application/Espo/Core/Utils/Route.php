<?php


namespace Espo\Core\Utils;

use Espo\Core\Api\Route as RouteItem;
use Espo\Core\Utils\File\Manager as FileManager;
use Espo\Core\Utils\Resource\PathProvider;


class Route
{
    
    private $data = null;
    private string $cacheKey = 'routes';
    private string $routesFileName = 'routes.json';

    public function __construct(
        private Config $config,
        private Metadata $metadata,
        private FileManager $fileManager,
        private DataCache $dataCache,
        private PathProvider $pathProvider
    ) {}

    
    public function getFullList(): array
    {
        if (!isset($this->data)) {
            $this->init();
        }

        assert($this->data !== null);

        return array_map(
            function (array $item): RouteItem {
                return new RouteItem(
                    $item['method'],
                    $item['route'],
                    $item['adjustedRoute'],
                    $item['params'] ?? [],
                    $item['noAuth'] ?? false,
                    $item['actionClassName'] ?? null
                );
            },
            $this->data
        );
    }

    private function init(): void
    {
        $useCache = $this->config->get('useCache');

        if ($this->dataCache->has($this->cacheKey) && $useCache) {
            
            $data = $this->dataCache->get($this->cacheKey);

            $this->data = $data;

            return;
        }

        $this->data = $this->unify();

        if ($useCache) {
            $this->dataCache->store($this->cacheKey, $this->data);
        }
    }

    
    private function unify(): array
    {
        $customData = $this->addDataFromFile([], $this->pathProvider->getCustom() . $this->routesFileName);

        $moduleData = [];

        foreach ($this->metadata->getModuleList() as $moduleName) {
            $moduleFilePath = $this->pathProvider->getModule($moduleName) . $this->routesFileName;

            foreach ($this->addDataFromFile([], $moduleFilePath) as $item) {
                $key = $item['method'] . $item['route'];

                $moduleData[$key] = $item;
            }
        }

        $data = array_merge($customData, array_values($moduleData));

        return $this->addDataFromFile(
            $data,
            $this->pathProvider->getCore() . $this->routesFileName
        );
    }

    
    private function addDataFromFile(array $currentData, string $routeFile): array
    {
        if (!$this->fileManager->exists($routeFile)) {
            return $currentData;
        }

        $content = $this->fileManager->getContents($routeFile);

        $data = Json::decode($content, true);

        return $this->appendRoutesToData($currentData, $data);
    }

    
    private function appendRoutesToData(array $data, array $newData): array
    {
        foreach ($newData as $route) {
            $route['adjustedRoute'] = $this->adjustPath($route['route']);

            if (isset($route['conditions'])) {
                $route['noAuth'] = !($route['conditions']['auth'] ?? true);

                unset($route['conditions']);
            }

            if (self::isRouteInList($route, $data)) {
                continue;
            }

            $data[] = $route;
        }

        return $data;
    }

    
    private function adjustPath(string $path): string
    {
        
        
        $pathFormatted = preg_replace('/\:([a-zA-Z0-9]+)/', '{${1}}', trim($path));

        if (!str_starts_with($pathFormatted, '/')) {
            return '/' . $pathFormatted;
        }

        return $pathFormatted;
    }

    public static function detectBasePath(): string
    {
        
        $serverScriptName = $_SERVER['SCRIPT_NAME'];

        
        $serverRequestUri = $_SERVER['REQUEST_URI'];

        
        $scriptName = parse_url($serverScriptName , PHP_URL_PATH);

        $scriptNameModified = str_replace('public/api/', 'api/', $scriptName);

        $scriptDir = dirname($scriptNameModified);

        
        $uri = parse_url('http:

        if (stripos($uri, $scriptName) === 0) {
            return $scriptName;
        }

        if ($scriptDir !== '/' && stripos($uri, $scriptDir) === 0) {
            return $scriptDir;
        }

        return '';
    }

    public static function detectEntryPointRoute(): string
    {
        $basePath = self::detectBasePath();

        
        $serverRequestUri = $_SERVER['REQUEST_URI'];

        
        $uri = parse_url('http:

        if ($uri === $basePath) {
            return '/';
        }

        if (stripos($uri, $basePath) === 0) {
            return substr($uri, strlen($basePath));
        }

        return '/';
    }

    
    static private function isRouteInList(array $newRoute, array $routeList): bool
    {
        foreach ($routeList as $route) {
            if (Util::areEqual($route, $newRoute)) {
                return true;
            }
        }

        return false;
    }
}
