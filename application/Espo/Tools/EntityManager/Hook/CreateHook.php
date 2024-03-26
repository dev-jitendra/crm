<?php


namespace Espo\Tools\EntityManager\Hook;

use Espo\Tools\EntityManager\Params;

interface CreateHook
{
    public function process(Params $params): void;
}
