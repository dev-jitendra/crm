<?php


namespace Espo\Core\Utils\Module;

use Espo\Core\Utils\Module;

class PathProvider
{
    private string $corePath = 'application/Espo/';
    private string $customPath = 'custom/Espo/Custom/';

    public function __construct(private Module $module)
    {}

    public function getCore(): string
    {
        return $this->corePath;
    }

    public function getCustom(): string
    {
        return $this->customPath;
    }

    public function getModule(string $moduleName): string
    {
        return $this->module->getModulePath($moduleName) . '/';
    }
}
