<?php


namespace Espo\Core\Rebuild\Actions;

use Espo\Core\Exceptions\Error;
use Espo\Core\Rebuild\RebuildAction;

use Espo\Core\Utils\Config\Access;
use Espo\Core\Utils\Metadata;


class ConfigMetadataCheck implements RebuildAction
{
    public function __construct(private Metadata $metadata)
    {}

    
    public function process(): void
    {
        $levelList = [
            Access::LEVEL_DEFAULT,
            Access::LEVEL_INTERNAL,
            Access::LEVEL_ADMIN,
            Access::LEVEL_GLOBAL,
            Access::LEVEL_SUPER_ADMIN,
            Access::LEVEL_SYSTEM,
        ];

        $params = $this->metadata->get(['app', 'config', 'params']) ?? [];

        foreach ($params as $name => $item) {
            $level = $item['level'] ?? null;

            if ($level !== null && !in_array($level, $levelList)) {
                throw new Error("Config parameter '{$name}' has not a allowed level in app > config.");
            }
        }
    }
}
