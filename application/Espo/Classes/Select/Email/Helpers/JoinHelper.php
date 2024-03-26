<?php


namespace Espo\Classes\Select\Email\Helpers;

use Espo\Entities\Email;
use Espo\ORM\Query\SelectBuilder as QueryBuilder;

class JoinHelper
{
    public function joinEmailUser(QueryBuilder $queryBuilder, string $userId): void
    {
        if ($queryBuilder->hasLeftJoinAlias('emailUser')) {
            return;
        }

        $queryBuilder->leftJoin(Email::RELATIONSHIP_EMAIL_USER, 'emailUser', [
            'emailUser.emailId:' => 'id',
            'emailUser.deleted' => false,
            'emailUser.userId' => $userId,
        ]);
    }
}
