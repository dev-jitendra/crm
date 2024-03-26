<?php


namespace Espo\Core\Select\AccessControl;


interface FilterResolver
{
    public function resolve(): ?string;
}
