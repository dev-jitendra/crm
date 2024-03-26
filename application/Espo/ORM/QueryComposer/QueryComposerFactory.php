<?php


namespace Espo\ORM\QueryComposer;

interface QueryComposerFactory
{
    public function create(string $platform): QueryComposer;
}
