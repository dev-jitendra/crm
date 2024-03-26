<?php


namespace Espo\Core\Rebuild;

interface RebuildAction
{
    public function process(): void;
}
