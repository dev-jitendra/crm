<?php


namespace Espo\Core\Select\AccessControl\Filters;

use Espo\Core\Select\AccessControl\Filter;
use Espo\ORM\Query\SelectBuilder;
use Espo\Core\Utils\Metadata;
use Espo\ORM\Defs;
use Espo\Entities\User;

use LogicException;

class ForeignOnlyTeam implements Filter
{
    private string $entityType;
    private User $user;
    private Metadata $metadata;
    private Defs $defs;

    public function __construct(string $entityType, User $user, Metadata $metadata, Defs $defs)
    {
        $this->user = $user;
        $this->entityType = $entityType;
        $this->metadata = $metadata;
        $this->defs = $defs;
    }

    public function apply(SelectBuilder $queryBuilder): void
    {
        $link = $this->metadata->get(['aclDefs', $this->entityType, 'link']);

        if (!$link) {
            throw new LogicException("No `link` in aclDefs for {$this->entityType}.");
        }

        $alias = $link . 'Access';

        $queryBuilder->leftJoin($link, $alias);

        $ownerAttribute = $this->getOwnerAttribute($link);

        if (!$ownerAttribute) {
            $queryBuilder->where(['id' => null]);

            return;
        }

        $teamIdList = $this->user->getTeamIdList();

        if (count($teamIdList) === 0) {
            $queryBuilder->where([
                "{$alias}.{$ownerAttribute}" => $this->user->getId(),
            ]);

            return;
        }

        $foreignEntityType = $this->defs
            ->getEntity($this->entityType)
            ->getRelation($link)
            ->getForeignEntityType();

        $queryBuilder
            ->distinct()
            ->leftJoin(
                'EntityTeam',
                'entityTeamAccess',
                [
                    'entityTeamAccess.entityType' => $foreignEntityType,
                    'entityTeamAccess.entityId:' => "{$alias}.id",
                    'entityTeamAccess.deleted' => false,
                ]
            )
            ->where([
                'OR' => [
                    'entityTeamAccess.teamId' => $teamIdList,
                    "{$alias}.{$ownerAttribute}" => $this->user->getId(),
                ],
                "{$alias}.id!=" => null,
            ]);
    }

    private function getOwnerAttribute(string $link): ?string
    {
        $foreignEntityType = $this->defs
            ->getEntity($this->entityType)
            ->getRelation($link)
            ->getForeignEntityType();

        $foreignEntityDefs = $this->defs->getEntity($foreignEntityType);

        if ($foreignEntityDefs->hasField('assignedUser')) {
            return 'assignedUserId';
        }

        if ($foreignEntityDefs->hasField('createdBy')) {
            return 'createdById';
        }

        return null;
    }
}
