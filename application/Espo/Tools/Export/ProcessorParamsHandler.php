<?php


namespace Espo\Tools\Export;

interface ProcessorParamsHandler
{
    public function handle(Params $params, Processor\Params $processorParams): Processor\Params;
}
