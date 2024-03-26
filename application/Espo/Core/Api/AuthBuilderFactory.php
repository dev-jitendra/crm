<?php


namespace Espo\Core\Api;

use Espo\Core\InjectableFactory;

class AuthBuilderFactory
{

    public function __construct(private InjectableFactory $injectableFactory)
    {}

    public function create(): AuthBuilder
    {
        return $this->injectableFactory->create(AuthBuilder::class);
    }
}
