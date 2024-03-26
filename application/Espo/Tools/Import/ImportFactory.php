<?php


namespace Espo\Tools\Import;

use Espo\Core\InjectableFactory;

class ImportFactory
{
    public function __construct(private InjectableFactory $injectableFactory)
    {}

    public function create(): Import
    {
        return $this->injectableFactory->create(Import::class);
    }
}
