<?php


namespace Espo\ORM\Query\Part;


interface WhereItem
{
    
    public function getRaw(): array;

    public function getRawKey(): string;

    public function getRawValue(): mixed;
}
