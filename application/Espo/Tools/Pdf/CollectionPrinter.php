<?php


namespace Espo\Tools\Pdf;

use Espo\ORM\Collection;

interface CollectionPrinter
{
    
    public function print(Template $template, Collection $collection, Params $params, IdDataMap $idDataMap): Contents;
}
