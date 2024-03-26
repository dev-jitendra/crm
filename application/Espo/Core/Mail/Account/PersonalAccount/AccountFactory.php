<?php


namespace Espo\Core\Mail\Account\PersonalAccount;

use Espo\Core\Exceptions\Error;
use Espo\Core\InjectableFactory;
use Espo\Core\Binding\BindingContainerBuilder;
use Espo\Entities\EmailAccount;
use Espo\ORM\EntityManager;

class AccountFactory
{
    public function __construct(
        private InjectableFactory $injectableFactory,
        private EntityManager $entityManager
    ) {}

    
    public function create(string $id): Account
    {
        $entity = $this->entityManager->getEntityById(EmailAccount::ENTITY_TYPE, $id);

        if (!$entity) {
            throw new Error("EmailAccount '{$id}' not found.");
        }

        $binding = BindingContainerBuilder::create()
            ->bindInstance(EmailAccount::class, $entity)
            ->build();

        return $this->injectableFactory->createWithBinding(Account::class, $binding);
    }
}
