<?php


namespace Espo\Core\Utils\Config;

use Espo\Core\Utils\Config;
use Espo\Core\Utils\Metadata;

class InternalConfigHelper
{
    public function __construct(private Config $config, private Metadata $metadata)
    {}

    public function isParamForInternalConfig(string $name): bool
    {
        if ($this->config->isInternal($name)) {
            return true;
        }

        if (in_array($name, $this->config->get('systemItems') ?? [])) {
            return true;
        }

        $level = $this->metadata->get(['app', 'config', 'params', $name, 'level']);

        if ($level === Access::LEVEL_SYSTEM || $level === Access::LEVEL_INTERNAL) {
            return true;
        }

        return false;
    }
}
