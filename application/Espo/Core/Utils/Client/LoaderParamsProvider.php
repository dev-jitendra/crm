<?php


namespace Espo\Core\Utils\Client;

use Espo\Core\Utils\Metadata;

class LoaderParamsProvider
{
    public function __construct(
        private Metadata $metadata
    ) {}

    public function getLibsConfig(): object
    {
        return (object) $this->metadata->get(['app', 'jsLibs'], []);
    }

    public function getAliasMap(): object
    {
        $map = (object) [];

        
        $libs = $this->metadata->get(['app', 'jsLibs'], []);

        foreach ($libs as $name => $item) {
            
            $aliases = $item['aliases'] ?? null;

            $map->$name = 'lib!' . $name;

            if ($aliases) {
                foreach ($aliases as $alias) {
                    $map->$alias = 'lib!' . $name;
                }
            }
        }

        return $map;
    }
}
