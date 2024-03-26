<?php


namespace Espo\Core\Acl;

use Espo\Core\Interfaces\Injectable;

use Espo\ORM\Entity;
use Espo\Entities\User;
use Espo\Core\Acl\AccessChecker\ScopeChecker;
use Espo\Core\Acl\AccessChecker\ScopeCheckerData;
use Espo\Core\AclManager;
use Espo\Core\ORM\EntityManager;
use Espo\Core\Utils\Config;


class Base implements AccessChecker, Injectable
{
    protected $dependencyList = []; 

    protected $dependencies = []; 

    protected $injections = []; 

    protected $entityManager; 

    protected $aclManager; 

    protected $config; 

    protected $defaultChecker; 

    protected $scopeChecker; 

    public function __construct(
        EntityManager $entityManager,
        AclManager $aclManager,
        Config $config,
        DefaultAccessChecker $defaultChecker,
        ScopeChecker $scopeChecker
    ) {
        $this->entityManager = $entityManager;
        $this->aclManager = $aclManager;
        $this->config = $config;
        $this->defaultChecker = $defaultChecker;
        $this->scopeChecker = $scopeChecker;

        $this->init();
    }

    public function check(User $user, ScopeData $data): bool
    {
        return $this->defaultChecker->check($user, $data);
    }

    public function checkEntity(User $user, Entity $entity, ScopeData $data, string $action): bool
    {
        $checkerData = ScopeCheckerData
            ::createBuilder()
            ->setIsOwnChecker(
                function () use ($user, $entity): bool {
                    return (bool) $this->checkIsOwner($user, $entity);
                }
            )
            ->setInTeamChecker(
                function () use ($user, $entity): bool {
                    return (bool) $this->checkInTeam($user, $entity);
                }
            )
            ->build();

        return $this->scopeChecker->check($data, $action, $checkerData);
    }

    public function checkScope(User $user, ScopeData $data, ?string $action = null): bool
    {
        if (!$action) {
            return $this->defaultChecker->check($user, $data);
        }

        if ($action === Table::ACTION_CREATE) {
            return $this->defaultChecker->checkCreate($user, $data);
        }

        if ($action === Table::ACTION_READ) {
            return $this->defaultChecker->checkRead($user, $data);
        }

        if ($action === Table::ACTION_EDIT) {
            return $this->defaultChecker->checkEdit($user, $data);
        }

        if ($action === Table::ACTION_DELETE) {
            return $this->defaultChecker->checkDelete($user, $data);
        }

        if ($action === Table::ACTION_STREAM) {
            return $this->defaultChecker->checkStream($user, $data);
        }

        return false;
    }

    public function checkIsOwner(User $user, Entity $entity) 
    {
        return $this->aclManager->checkOwnershipOwn($user, $entity);
    }

    public function checkInTeam(User $user, Entity $entity) 
    {
        return $this->aclManager->checkOwnershipTeam($user, $entity);
    }

    public function inject($name, $object) 
    {
        $this->injections[$name] = $object;
    }

    protected function init() 
    {
    }

    protected function getInjection($name) 
    {
        return $this->injections[$name] ?? $this->$name ?? null;
    }

    protected function addDependencyList(array $list) 
    {
        foreach ($list as $item) {
            $this->addDependency($item);
        }
    }

    protected function addDependency($name) 
    {
        $this->dependencyList[] = $name;
    }

    public function getDependencyList() 
    {
        return array_merge($this->dependencyList, $this->dependencies);
    }

    protected function getConfig(): Config
    {
        return $this->config;
    }

    protected function getEntityManager(): EntityManager
    {
        return $this->entityManager;
    }

    protected function getAclManager(): AclManager
    {
        return $this->aclManager;
    }
}
