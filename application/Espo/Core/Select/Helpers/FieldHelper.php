<?php


namespace Espo\Core\Select\Helpers;

use Espo\ORM\EntityManager;
use Espo\ORM\Entity;
use Espo\ORM\BaseEntity;


class FieldHelper
{
    private ?Entity $seed = null;

    public function __construct(private string $entityType, private EntityManager $entityManager)
    {}

    private function getSeed(): Entity
    {
        return $this->seed ?? $this->entityManager->getNewEntity($this->entityType);
    }

    public function hasAssignedUsersField(): bool
    {
        if (
            $this->getSeed()->hasRelation('assignedUsers') &&
            $this->getSeed()->hasAttribute('assignedUsersIds')
        ) {
            return true;
        }

        return false;
    }

    public function hasAssignedUserField(): bool
    {
        if ($this->getSeed()->hasAttribute('assignedUserId')) {
            return true;
        }

        return false;
    }

    public function hasCreatedByField(): bool
    {
        if ($this->getSeed()->hasAttribute('createdById')) {
            return true;
        }

        return false;
    }

    public function hasTeamsField(): bool
    {
        if (
            $this->getSeed()->hasRelation('teams') &&
            $this->getSeed()->hasAttribute('teamsIds')
        ) {
            return true;
        }

        return false;
    }

    public function hasContactField(): bool
    {
        return
            $this->getSeed()->hasAttribute('contactId') &&
            $this->getRelationParam($this->getSeed(), 'contact', 'entity') === 'Contact';
    }

    public function hasContactsRelation(): bool
    {
        return
            $this->getSeed()->hasRelation('contacts') &&
            $this->getRelationParam($this->getSeed(), 'contacts', 'entity') === 'Contact';
    }

    public function hasParentField(): bool
    {
        return
            $this->getSeed()->hasAttribute('parentId') &&
            $this->getSeed()->hasRelation('parent');
    }

    public function hasAccountField(): bool
    {
        return
            $this->getSeed()->hasAttribute('accountId') &&
            $this->getRelationParam($this->getSeed(), 'account', 'entity') === 'Account';
    }

    public function hasAccountsRelation(): bool
    {
        return
            $this->getSeed()->hasRelation('accounts') &&
            $this->getRelationParam($this->getSeed(), 'accounts', 'entity') === 'Account';
    }

    
    private function getRelationParam(Entity $entity, string $relation, string $param)
    {
        if ($entity instanceof BaseEntity) {
            return $entity->getRelationParam($relation, $param);
        }

        $entityDefs = $this->entityManager
            ->getDefs()
            ->getEntity($entity->getEntityType());

        if (!$entityDefs->hasRelation($relation)) {
            return null;
        }

        return $entityDefs->getRelation($relation)->getParam($param);
    }
}
