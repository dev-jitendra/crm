<?php


namespace Espo\Tools\LinkManager\Hook;

use Espo\Tools\LinkManager\Params;

interface DeleteHook
{
    public function process(Params $params): void;
}
