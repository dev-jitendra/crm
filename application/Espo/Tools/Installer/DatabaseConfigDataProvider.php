<?php


namespace Espo\Tools\Installer;

use Espo\Core\Utils\Database\ConfigDataProvider;

class DatabaseConfigDataProvider implements ConfigDataProvider
{
    public function __construct(
        private string $platform
    ) {}

    public function getPlatform(): string
    {
        return $this->platform;
    }
}
