<?php


namespace Espo\Core\Select\AccessControl\FilterResolvers;

use Espo\Core\Select\AccessControl\FilterResolver;

class Bypass implements FilterResolver
{
    public function resolve(): ?string
    {
        return null;
    }
}
