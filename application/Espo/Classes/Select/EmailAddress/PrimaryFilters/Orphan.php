<?php


namespace Espo\Classes\Select\EmailAddress\PrimaryFilters;

use Espo\Core\Select\Primary\Filter;
use Espo\ORM\Query\SelectBuilder;

class Orphan implements Filter
{
    public function apply(SelectBuilder $queryBuilder): void
    {
        $queryBuilder
            ->distinct()
            ->leftJoin(
                'EntityEmailAddress',
                'entityEmailAddress',
                [
                    'emailAddressId:' => 'id',
                    'deleted' => false,
                ]
            )
            ->leftJoin(
                'EmailEmailAddress',
                'emailEmailAddress',
                [
                    'emailAddressId:' => 'id',
                    'deleted' => false,
                ]
            )
            ->leftJoin(
                'Email',
                'email',
                [
                    'fromEmailAddressId:' => 'id',
                    'deleted' => false,
                ]
            )
            ->where([
                'entityEmailAddress.id' => null,
                'emailEmailAddress.id' => null,
                'email.id' => null,
            ]);
    }
}
