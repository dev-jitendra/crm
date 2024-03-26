<?php


namespace Espo\ORM\QueryComposer\Part;

interface FunctionConverter
{
    public function convert(string ...$argumentList): string;
}
