<?php


namespace Espo\Core\Container;

use Espo\Core\Container;
use Espo\Core\Container\Container as ContainerInterface;

use Espo\Core\Binding\BindingContainer;
use Espo\Core\Binding\BindingLoader;
use Espo\Core\Binding\EspoBindingLoader;

use Espo\Core\Utils\File\Manager as FileManager;
use Espo\Core\Utils\Config\ConfigFileManager;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\DataCache;
use Espo\Core\Utils\Module;

use Espo\Core\Loaders\Log as LogLoader;
use Espo\Core\Loaders\DataManager as DataManagerLoader;
use Espo\Core\Loaders\Metadata as MetadataLoader;


class ContainerBuilder
{
    
    private string $containerClassName = Container::class;
    
    private string $containerConfigurationClassName = ContainerConfiguration::class;
    
    private string $configClassName = Config::class;
    
    private string $fileManagerClassName = FileManager::class;
    
    private string $dataCacheClassName = DataCache::class;
    
    private string $moduleClassName = Module::class;
    private ?BindingLoader $bindingLoader = null;
    
    private $services = [];
    
    protected $loaderClassNames = [
        'log' => LogLoader::class,
        'dataManager' => DataManagerLoader::class,
        'metadata' => MetadataLoader::class,
    ];

    public function withBindingLoader(BindingLoader $bindingLoader): self
    {
        $this->bindingLoader = $bindingLoader;

        return $this;
    }

    
    public function withServices(array $services): self
    {
        foreach ($services as $key => $value) {
            $this->services[$key] = $value;
        }

        return $this;
    }

    
    public function withLoaderClassNames(array $classNames): self
    {
        foreach ($classNames as $key => $value) {
            $this->loaderClassNames[$key] = $value;
        }

        return $this;
    }

    
    public function withContainerClassName(string $containerClassName): self
    {
        $this->containerClassName = $containerClassName;

        return $this;
    }

    
    public function withContainerConfigurationClassName(string $containerConfigurationClassName): self
    {
        $this->containerConfigurationClassName = $containerConfigurationClassName;

        return $this;
    }

    
    public function withConfigClassName(string $configClassName): self
    {
        $this->configClassName = $configClassName;

        return $this;
    }

    
    public function withFileManagerClassName(string $fileManagerClassName): self
    {
        $this->fileManagerClassName = $fileManagerClassName;

        return $this;
    }

    
    public function withDataCacheClassName(string $dataCacheClassName): self
    {
        $this->dataCacheClassName = $dataCacheClassName;

        return $this;
    }

    public function build(): ContainerInterface
    {
        
        $config = $this->services['config'] ?? (
            new $this->configClassName(
                new ConfigFileManager()
            )
        );

        $fileManager = $this->services['fileManager'] ?? (
            new $this->fileManagerClassName(
                $config->get('defaultPermissions')
            )
        );

        $dataCache = $this->services['dataCache'] ?? (
            new $this->dataCacheClassName($fileManager)
        );

        $useCache = $config->get('useCache') ?? false;

        
        $module = $this->services['module'] ?? (
            new $this->moduleClassName($fileManager, $dataCache, $useCache)
        );

        $this->services['config'] = $config;
        $this->services['fileManager'] = $fileManager;
        $this->services['dataCache'] = $dataCache;
        $this->services['module'] = $module;

        $bindingLoader = $this->bindingLoader ?? (
            new EspoBindingLoader($module)
        );

        $bindingContainer = new BindingContainer($bindingLoader->load());

        return new $this->containerClassName(
            $this->containerConfigurationClassName,
            $bindingContainer,
            $this->loaderClassNames,
            $this->services
        );
    }
}
