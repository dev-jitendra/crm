<?php


namespace Espo\Tools\EntityManager\Hook;

use Espo\Tools\EntityManager\Params;

interface DeleteHook
{
    public function process(Params $params): void;
}
