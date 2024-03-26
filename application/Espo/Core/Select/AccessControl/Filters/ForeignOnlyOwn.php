<?php


namespace Espo\Core\Select\AccessControl\Filters;

use Espo\Core\Select\AccessControl\Filter;
use Espo\ORM\Defs;
use Espo\ORM\Query\SelectBuilder;
use Espo\Core\Utils\Metadata;
use Espo\Entities\User;

use LogicException;

class ForeignOnlyOwn implements Filter
{
    public function __construct(
        private string $entityType,
        private User $user,
        private Metadata $metadata,
        private Defs $defs
    ) {}

    public function apply(SelectBuilder $queryBuilder): void
    {
        $link = $this->metadata->get(['aclDefs', $this->entityType, 'link']);

        if (!$link) {
            throw new LogicException("No `link` in aclDefs for {$this->entityType}.");
        }

        $alias = $link . 'Access';

        $queryBuilder->leftJoin($link, $alias);

        $foreignEntityType = $this->defs
            ->getEntity($this->entityType)
            ->getRelation($link)
            ->getForeignEntityType();

        $foreignEntityDefs = $this->defs->getEntity($foreignEntityType);

        if ($foreignEntityDefs->hasField('assignedUser')) {
            $queryBuilder->where([
                "{$alias}.assignedUserId" => $this->user->getId(),
            ]);

            return;
        }

        if ($foreignEntityDefs->hasField('createdBy')) {
            $queryBuilder->where([
                "{$alias}.createdById" => $this->user->getId(),
            ]);

            return;
        }

        $queryBuilder->where(['id' => null]);
    }
}
