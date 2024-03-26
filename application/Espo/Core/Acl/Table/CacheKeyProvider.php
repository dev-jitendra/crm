<?php


namespace Espo\Core\Acl\Table;

interface CacheKeyProvider
{
    public function get(): string;
}
