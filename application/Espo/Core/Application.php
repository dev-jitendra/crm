<?php


namespace Espo\Core;

use Espo\Core\Application\Runner;
use Espo\Core\Application\RunnerParameterized;
use Espo\Core\Container\ContainerBuilder;
use Espo\Core\Application\RunnerRunner;
use Espo\Core\Application\Runner\Params as RunnerParams;
use Espo\Core\Application\Exceptions\RunnerException;
use Espo\Core\Utils\Autoload;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\Metadata;
use Espo\Core\Utils\ClientManager;


class Application
{
    protected Container $container;

    public function __construct()
    {
        date_default_timezone_set('UTC');

        $this->initContainer();
        $this->initAutoloads();
        $this->initPreloads();
    }

    protected function initContainer(): void
    {
        
        $container = (new ContainerBuilder())->build();

        $this->container = $container;
    }

    
    public function run(string $className, ?RunnerParams $params = null): void
    {
        $runnerRunner = $this->getInjectableFactory()->create(RunnerRunner::class);

        try {
            $runnerRunner->run($className, $params);
        }
        catch (RunnerException $e) {
            die($e->getMessage());
        }
    }

    
    public function isInstalled(): bool
    {
        return $this->getConfig()->get('isInstalled');
    }

    
    public function getContainer(): Container
    {
        return $this->container;
    }

    protected function getInjectableFactory(): InjectableFactory
    {
        return $this->container->getByClass(InjectableFactory::class);
    }

    protected function getApplicationUser(): ApplicationUser
    {
        return $this->container->getByClass(ApplicationUser::class);
    }

    protected function getClientManager(): ClientManager
    {
        return $this->container->getByClass(ClientManager::class);
    }

    protected function getMetadata(): Metadata
    {
        return $this->container->getByClass(Metadata::class);
    }

    protected function getConfig(): Config
    {
        return $this->container->getByClass(Config::class);
    }

    protected function initAutoloads(): void
    {
        $autoload = $this->getInjectableFactory()->create(Autoload::class);

        $autoload->register();
    }

    
    protected function initPreloads(): void
    {
        foreach ($this->getMetadata()->get(['app', 'containerServices']) ?? [] as $name => $defs) {
            if ($defs['preload'] ?? false) {
                $this->container->get($name);
            }
        }
    }

    
    public function setClientBasePath(string $basePath): void
    {
        $this->getClientManager()->setBasePath($basePath);
    }

    
    public function setupSystemUser(): void
    {
        $this->getApplicationUser()->setupSystemUser();
    }
}
