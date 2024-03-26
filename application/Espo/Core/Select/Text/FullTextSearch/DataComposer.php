<?php


namespace Espo\Core\Select\Text\FullTextSearch;

use Espo\Core\Select\Text\FullTextSearch\DataComposer\Params;

interface DataComposer
{
    public function compose(string $filter, Params $params): ?Data;
}
