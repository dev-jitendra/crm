<?php


namespace Espo\Core\Authentication\TwoFactor;

use Espo\Core\InjectableFactory;
use Espo\Core\Utils\Metadata;

use LogicException;

class LoginFactory
{
    private InjectableFactory $injectableFactory;
    private Metadata $metadata;

    public function __construct(InjectableFactory $injectableFactory, Metadata $metadata)
    {
        $this->injectableFactory = $injectableFactory;
        $this->metadata = $metadata;
    }

    public function create(string $method): Login
    {
        
        $className = $this->metadata->get(['app', 'authentication2FAMethods', $method, 'loginClassName']);

        if (!$className) {
            throw new LogicException("No login-class class for '{$method}'.");
        }

        return $this->injectableFactory->create($className);
    }
}
