<?php


namespace Espo\Tools\LinkManager\Hook;

use Espo\Tools\LinkManager\Params;

interface CreateHook
{
    public function process(Params $params): void;
}
