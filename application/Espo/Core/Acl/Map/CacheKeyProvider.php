<?php


namespace Espo\Core\Acl\Map;

interface CacheKeyProvider
{
    public function get(): string;
}
