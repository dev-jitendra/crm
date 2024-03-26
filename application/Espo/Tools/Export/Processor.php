<?php


namespace Espo\Tools\Export;

use Psr\Http\Message\StreamInterface;

use Espo\Tools\Export\Processor\Params;

interface Processor
{
    public function process(Params $params, Collection $collection): StreamInterface;
}
