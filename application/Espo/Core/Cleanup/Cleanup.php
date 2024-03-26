<?php


namespace Espo\Core\Cleanup;

interface Cleanup
{
    public function process(): void;
}
