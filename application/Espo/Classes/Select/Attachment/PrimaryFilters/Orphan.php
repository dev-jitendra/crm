<?php


namespace Espo\Classes\Select\Attachment\PrimaryFilters;

use Espo\Core\Select\Primary\Filter;
use Espo\ORM\Query\SelectBuilder;

class Orphan implements Filter
{
    public function apply(SelectBuilder $queryBuilder): void
    {
        $queryBuilder->where([
            'role' => ['Attachment', 'Inline Attachment'],
            [
                'OR' => [
                    [
                        'parentId' => null,
                        'parentType!=' => null,
                        'relatedType=' => null,
                    ],
                    [
                        'parentType' => null,
                        'relatedId' => null,
                        'relatedType!=' => null,
                    ],
                ],
            ],
            [
                'OR' => [
                    'relatedType!=' => 'Settings',
                    'relatedType=' => null,
                ],
            ],
            'attachmentChild.id' => null,
        ]);

        $queryBuilder->leftJoin(
            'Attachment',
            'attachmentChild',
            [
                'attachmentChild.sourceId:' => 'attachment.id',
                'attachmentChild.deleted' => false,
            ]
        );

        $queryBuilder->distinct();
    }
}
