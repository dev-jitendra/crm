<?php


namespace Espo\Tools\Export;

use Espo\Core\InjectableFactory;
use Espo\Entities\User;

use Espo\Core\AclManager;
use Espo\Core\Acl;

use Espo\Core\Binding\BindingContainerBuilder;

class Factory
{
    private InjectableFactory $injectableFactory;

    private AclManager $aclManager;

    public function __construct(InjectableFactory $injectableFactory, AclManager $aclManager)
    {
        $this->injectableFactory = $injectableFactory;
        $this->aclManager = $aclManager;
    }

    public function create(): Export
    {
        return $this->injectableFactory->create(Export::class);
    }

    public function createForUser(User $user): Export
    {
        $bindingContainer = BindingContainerBuilder::create()
            ->bindInstance(User::class, $user)
            ->bindInstance(Acl::class, $this->aclManager->createUserAcl($user))
            ->build();

        return $this->injectableFactory->createWithBinding(Export::class, $bindingContainer);
    }
}
