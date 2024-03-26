<?php


namespace Espo\Core\Utils\Config;

class ConfigWriterHelper
{
    public function generateCacheTimestamp(): int
    {
        return time();
    }

    public function generateMicrotime(): float
    {
        return microtime(true);
    }
}
