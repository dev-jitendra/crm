<?php


namespace Espo\Core\Mail\Account\GroupAccount;

use Espo\Core\Exceptions\Error;
use Espo\Core\InjectableFactory;
use Espo\Core\Binding\BindingContainerBuilder;
use Espo\Entities\InboundEmail;
use Espo\ORM\EntityManager;

class AccountFactory
{

    public function __construct(
        private InjectableFactory $injectableFactory,
        private EntityManager $entityManager
    ) {}

    
    public function create(string $id): Account
    {
        $entity = $this->entityManager->getEntityById(InboundEmail::ENTITY_TYPE, $id);

        if (!$entity) {
            throw new Error("InboundEmail '{$id}' not found.");
        }

        $binding = BindingContainerBuilder::create()
            ->bindInstance(InboundEmail::class, $entity)
            ->build();

        return $this->injectableFactory->createWithBinding(Account::class, $binding);
    }
}
