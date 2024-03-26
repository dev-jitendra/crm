<?php


namespace Espo\Core\Authentication;

use Espo\Core\InjectableFactory;
use Espo\Core\Utils\Metadata;

use RuntimeException;

class LogoutFactory
{
    private InjectableFactory $injectableFactory;
    private Metadata $metadata;

    public function __construct(InjectableFactory $injectableFactory, Metadata $metadata)
    {
        $this->injectableFactory = $injectableFactory;
        $this->metadata = $metadata;
    }

    public function create(string $method): Logout
    {
        $className = $this->getClassName($method);

        if (!$className) {
            throw new RuntimeException();
        }

        return $this->injectableFactory->create($className);
    }

    public function isCreatable(string $method): bool
    {
        return (bool) $this->getClassName($method);
    }

    
    private function getClassName(string $method): ?string
    {
        return $this->metadata->get(['authenticationMethods', $method, 'logoutClassName']);
    }
}
