<?php


namespace Espo\Core\Select;

use Espo\Core\InjectableFactory;


class SelectBuilderFactory
{
    public function __construct(private InjectableFactory $injectableFactory)
    {}

    public function create(): SelectBuilder
    {
        return $this->injectableFactory->create(SelectBuilder::class);
    }
}
