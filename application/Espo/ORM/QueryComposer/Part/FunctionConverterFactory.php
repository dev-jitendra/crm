<?php


namespace Espo\ORM\QueryComposer\Part;

interface FunctionConverterFactory
{
    public function create(string $name): FunctionConverter;

    public function isCreatable(string $name): bool;
}
