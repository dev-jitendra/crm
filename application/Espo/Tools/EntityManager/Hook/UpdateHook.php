<?php


namespace Espo\Tools\EntityManager\Hook;

use Espo\Tools\EntityManager\Params;

interface UpdateHook
{
    public function process(Params $params, Params $previousParams): void;
}
