<?php


namespace Espo\Classes\Select\EmailFilter\AccessControlFilters;

use Espo\Core\Select\AccessControl\Filter;
use Espo\Entities\EmailAccount;
use Espo\Entities\User;
use Espo\ORM\EntityManager;
use Espo\ORM\Query\SelectBuilder as QueryBuilder;

class OnlyOwn implements Filter
{
    private User $user;
    private EntityManager $entityManager;

    public function __construct(User $user, EntityManager $entityManager)
    {
        $this->user = $user;
        $this->entityManager = $entityManager;
    }

    public function apply(QueryBuilder $queryBuilder): void
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

        $queryBuilder->where($part);
    }
}
