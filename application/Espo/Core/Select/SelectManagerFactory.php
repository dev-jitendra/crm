<?php


namespace Espo\Core\Select;

use Espo\Core\Utils\Acl\UserAclManagerProvider;

use Espo\Core\Acl;
use Espo\Core\InjectableFactory;
use Espo\Core\Utils\ClassFinder;

use Espo\Entities\User;


class SelectManagerFactory
{
    
    protected string $defaultClassName = SelectManager::class;

    private $user;

    private $acl;

    private $aclManagerProvider;

    private $injectableFactory;

    private $classFinder;

    public function __construct(
        User $user,
        Acl $acl,
        UserAclManagerProvider $aclManagerProvider,
        InjectableFactory $injectableFactory,
        ClassFinder $classFinder
    ) {
        $this->user = $user;
        $this->acl = $acl;
        $this->aclManagerProvider = $aclManagerProvider;
        $this->injectableFactory = $injectableFactory;
        $this->classFinder = $classFinder;
    }

    public function create(string $entityType, ?User $user = null): SelectManager
    {
        $className = $this->classFinder->find('SelectManagers', $entityType);

        if (!$className || !class_exists($className)) {
            $className = $this->defaultClassName;
        }

        

        if ($user) {
            $acl = $this->aclManagerProvider->get($user)->createUserAcl($user);
        }
        else {
            $acl = $this->acl;
            $user = $this->user;
        }

        $selectManager = $this->injectableFactory->createWith($className, [
            'user' => $user,
            'acl' => $acl,
        ]);

        $selectManager->setEntityType($entityType);

        return $selectManager;
    }
}
