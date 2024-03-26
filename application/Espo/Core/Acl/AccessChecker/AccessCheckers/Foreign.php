<?php


namespace Espo\Core\Acl\AccessChecker\AccessCheckers;

use Espo\Entities\User;

use Espo\ORM\Entity;

use Espo\Core\Utils\Metadata;

use Espo\Core\Acl\DefaultAccessChecker;
use Espo\Core\Acl\Traits\DefaultAccessCheckerDependency;
use Espo\Core\Acl\AccessEntityCreateChecker;
use Espo\Core\Acl\AccessEntityReadChecker;
use Espo\Core\Acl\AccessEntityEditChecker;
use Espo\Core\Acl\AccessEntityDeleteChecker;
use Espo\Core\Acl\AccessEntityStreamChecker;
use Espo\Core\Acl\ScopeData;

use Espo\ORM\EntityManager;

use LogicException;


class Foreign implements

    AccessEntityCreateChecker,
    AccessEntityReadChecker,
    AccessEntityEditChecker,
    AccessEntityDeleteChecker,
    AccessEntityStreamChecker
{
    use DefaultAccessCheckerDependency;

    private Metadata $metadata;
    private EntityManager $entityManager;

    public function __construct(
        Metadata $metadata,
        DefaultAccessChecker $defaultAccessChecker,
        EntityManager $entityManager
    ) {
        $this->metadata = $metadata;
        $this->defaultAccessChecker = $defaultAccessChecker;
        $this->entityManager = $entityManager;
    }

    private function getForeignEntity(Entity $entity): ?Entity
    {
        $entityType = $entity->getEntityType();

        $link = $this->metadata->get(['aclDefs', $entityType, 'link']);

        if (!$link) {
            throw new LogicException("No `link` in aclDefs for {$entityType}.");
        }

        if ($entity->isNew()) {
            $foreignEntityType = $this->entityManager
                ->getDefs()
                ->getEntity($entityType)
                ->getRelation($link)
                ->getForeignEntityType();

            
            $id = $entity->get($link . 'Id');

            if (!$id) {
                return null;
            }

            return $this->entityManager->getEntityById($foreignEntityType, $id);
        }

        return $this->entityManager
            ->getRDBRepository($entityType)
            ->getRelation($entity, $link)
            ->findOne();
    }

    public function checkEntityCreate(User $user, Entity $entity, ScopeData $data): bool
    {
        $foreign = $this->getForeignEntity($entity);

        if (!$foreign) {
            return false;
        }

        return $this->defaultAccessChecker->checkEntityCreate($user, $entity, $data);
    }

    public function checkEntityRead(User $user, Entity $entity, ScopeData $data): bool
    {
        $foreign = $this->getForeignEntity($entity);

        if (!$foreign) {
            return false;
        }

        return $this->defaultAccessChecker->checkEntityRead($user, $entity, $data);
    }

    public function checkEntityEdit(User $user, Entity $entity, ScopeData $data): bool
    {
        $foreign = $this->getForeignEntity($entity);

        if (!$foreign) {
            return false;
        }

        return $this->defaultAccessChecker->checkEntityEdit($user, $entity, $data);
    }

    public function checkEntityDelete(User $user, Entity $entity, ScopeData $data): bool
    {
        $foreign = $this->getForeignEntity($entity);

        if (!$foreign) {
            return false;
        }

        return $this->defaultAccessChecker->checkEntityDelete($user, $entity, $data);
    }

    public function checkEntityStream(User $user, Entity $entity, ScopeData $data): bool
    {
        $foreign = $this->getForeignEntity($entity);

        if (!$foreign) {
            return false;
        }

        return $this->defaultAccessChecker->checkEntityStream($user, $entity, $data);
    }
}
