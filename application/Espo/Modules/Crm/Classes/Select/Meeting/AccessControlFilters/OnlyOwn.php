<?php


namespace Espo\Modules\Crm\Classes\Select\Meeting\AccessControlFilters;

use Espo\Core\Select\AccessControl\Filter;
use Espo\ORM\Defs;
use Espo\ORM\Query\SelectBuilder;
use Espo\ORM\Query\Part\Condition as Cond;

use Espo\Entities\User;

class OnlyOwn implements Filter
{
    public function __construct(
        private User $user,
        private string $entityType,
        private Defs $defs
    ) {}

    public function apply(SelectBuilder $queryBuilder): void
    {
        $relationDefs = $this->defs
            ->getEntity($this->entityType)
            ->getRelation('users');

        $middleEntityType = ucfirst($relationDefs->getRelationshipName());
        $key1 = $relationDefs->getMidKey();

        $queryBuilder->where(
            Cond::in(
                Cond::column('id'),
                SelectBuilder::create()
                    ->select('id')
                    ->from($this->entityType)
                    ->leftJoin($middleEntityType, 'usersMiddle', [
                        "usersMiddle.{$key1}:" => 'id',
                        'usersMiddle.deleted' => false,
                    ])
                    ->where(
                        Cond::or(
                            Cond::equal(
                                Cond::column('usersMiddle.userId'),
                                $this->user->getId()
                            ),
                            Cond::equal(
                                Cond::column('assignedUserId'),
                                $this->user->getId()
                            )
                        )
                    )
                    ->build()
            )
        );
    }
}
