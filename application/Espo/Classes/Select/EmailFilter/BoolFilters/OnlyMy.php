<?php


namespace Espo\Classes\Select\EmailFilter\BoolFilters;

use Espo\Core\Select\Bool\Filter;
use Espo\Entities\EmailAccount;
use Espo\Entities\User;
use Espo\ORM\EntityManager;
use Espo\ORM\Query\Part\Where\OrGroupBuilder;
use Espo\ORM\Query\Part\WhereClause;
use Espo\ORM\Query\SelectBuilder as QueryBuilder;

class OnlyMy implements Filter
{
    public function __construct(private User $user, private EntityManager $entityManager)
    {}

    public function apply(QueryBuilder $queryBuilder, OrGroupBuilder $orGroupBuilder): void
    {
        $part = [];

        $part[] = [
            'parentType' => User::ENTITY_TYPE,
            'parentId' => $this->user->getId(),
        ];

        $idList = [];

        $emailAccountList = $this->entityManager
            ->getRDBRepository(EmailAccount::ENTITY_TYPE)
            ->select('id')
            ->where([
                'assignedUserId' => $this->user->getId(),
            ])
            ->find();

        foreach ($emailAccountList as $emailAccount) {
            $idList[] = $emailAccount->getId();
        }

        if (count($idList)) {
            $part = [
                'OR' => [
                    $part,
                    [
                        'parentType' => EmailAccount::ENTITY_TYPE,
                        'parentId' => $idList,
                    ],
                ]
            ];
        }

        $orGroupBuilder->add(WhereClause::fromRaw($part));
    }
}
