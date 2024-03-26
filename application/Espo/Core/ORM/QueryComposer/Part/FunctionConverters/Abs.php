<?php


namespace Espo\Core\ORM\QueryComposer\Part\FunctionConverters;

use Espo\ORM\QueryComposer\Part\FunctionConverter;

class Abs implements FunctionConverter
{
    public function convert(string ...$argumentList): string
    {
        return 'ABS(' . implode(', ', $argumentList) . ')';
    }
}
