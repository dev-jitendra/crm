<?php


namespace Espo\Core\Utils\Resource;

use Espo\Core\Utils\Module\PathProvider as ModulePathProvider;

class PathProvider
{
    public function __construct(private ModulePathProvider $provider)
    {}

    public function getCore(): string
    {
        return $this->provider->getCore() . 'Resources/';
    }

    public function getCustom(): string
    {
        return $this->provider->getCustom() . 'Resources/';
    }

    public function getModule(string $moduleName): string
    {
        return $this->provider->getModule($moduleName) . 'Resources/';
    }
}
