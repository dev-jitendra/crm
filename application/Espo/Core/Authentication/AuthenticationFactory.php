<?php


namespace Espo\Core\Authentication;

use Espo\Core\InjectableFactory;

class AuthenticationFactory
{
    public function __construct(private InjectableFactory $injectableFactory)
    {}

    public function create(): Authentication
    {
        return $this->injectableFactory->create(Authentication::class);
    }
}
