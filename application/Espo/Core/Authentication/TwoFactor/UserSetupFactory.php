<?php


namespace Espo\Core\Authentication\TwoFactor;

use Espo\Core\InjectableFactory;
use Espo\Core\Utils\Metadata;

use RuntimeException;

class UserSetupFactory
{

    public function __construct(
        private InjectableFactory $injectableFactory,
        private Metadata $metadata
    ) {}

    public function create(string $method): UserSetup
    {
        
        $className = $this->metadata->get(['app', 'authentication2FAMethods', $method, 'userSetupClassName']);

        if (!$className) {
            throw new RuntimeException("No user-setup class for '$method'.");
        }

        return $this->injectableFactory->create($className);
    }
}
