<?php


namespace Espo\Classes\Select\Email\Where\ItemConverters;

use Espo\Core\Select\Where\ItemConverter;
use Espo\Core\Select\Where\Item;

use Espo\Entities\Email;
use Espo\ORM\Query\SelectBuilder as QueryBuilder;
use Espo\ORM\Query\Part\WhereItem as WhereClauseItem;
use Espo\ORM\Query\Part\WhereClause;
use Espo\ORM\EntityManager;
use Espo\Entities\User;
use Espo\Classes\Select\Email\Helpers\JoinHelper;
use Espo\Tools\Email\Folder;

class InFolder implements ItemConverter
{
    public function __construct(
        private User $user,
        private EntityManager $entityManager,
        private JoinHelper $joinHelper
    ) {}

    public function convert(QueryBuilder $queryBuilder, Item $item): WhereClauseItem
    {
        $folderId = $item->getValue();

        return match ($folderId) {
            Folder::ALL => WhereClause::fromRaw([]),
            Folder::INBOX => $this->convertInbox($queryBuilder),
            Folder::IMPORTANT => $this->convertImportant($queryBuilder),
            Folder::SENT => $this->convertSent($queryBuilder),
            Folder::TRASH => $this->convertTrash($queryBuilder),
            Folder::DRAFTS => $this->convertDraft($queryBuilder),
            default => $this->convertFolderId($queryBuilder, $folderId),
        };
    }

    protected function convertInbox(QueryBuilder $queryBuilder): WhereClauseItem
    {
        $this->joinEmailUser($queryBuilder);

        $whereClause = [
            'emailUser.inTrash' => false,
            'emailUser.folderId' => null,
            'emailUser.userId' => $this->user->getId(),
            [
                'status' => [
                    Email::STATUS_ARCHIVED,
                    Email::STATUS_SENT,
                ],
                'groupFolderId' => null,
            ],
        ];

        $emailAddressIdList = $this->getEmailAddressIdList();

        if (!empty($emailAddressIdList)) {
            $whereClause['fromEmailAddressId!='] = $emailAddressIdList;

            $whereClause[] = [
                'OR' => [
                    'status' => Email::STATUS_ARCHIVED,
                    'createdById!=' => $this->user->getId(),
                ],
            ];
        }
        else {
            $whereClause[] = [
                'status' => Email::STATUS_ARCHIVED,
                'createdById!=' => $this->user->getId(),
            ];
        }

        return WhereClause::fromRaw($whereClause);
    }

    protected function convertSent(QueryBuilder $queryBuilder): WhereClauseItem
    {
        $this->joinEmailUser($queryBuilder);

        return WhereClause::fromRaw([
            'OR' => [
                'fromEmailAddressId' => $this->getEmailAddressIdList(),
                [
                    'status' => Email::STATUS_SENT,
                    'createdById' => $this->user->getId(),
                ]
            ],
            [
                'status!=' => Email::STATUS_DRAFT,
            ],
            'emailUser.inTrash' => false,
        ]);
    }

    protected function convertImportant(QueryBuilder $queryBuilder): WhereClauseItem
    {
        $this->joinEmailUser($queryBuilder);

        return WhereClause::fromRaw([
            'emailUser.userId' => $this->user->getId(),
            'emailUser.isImportant' => true,
        ]);
    }

    protected function convertTrash(QueryBuilder $queryBuilder): WhereClauseItem
    {
        $this->joinEmailUser($queryBuilder);

        return WhereClause::fromRaw([
            'emailUser.userId' => $this->user->getId(),
            'emailUser.inTrash' => true,
        ]);
    }

    protected function convertDraft(QueryBuilder $queryBuilder): WhereClauseItem
    {
        return WhereClause::fromRaw([
            'status' => Email::STATUS_DRAFT,
            'createdById' => $this->user->getId(),
        ]);
    }

    protected function convertFolderId(QueryBuilder $queryBuilder, string $folderId): WhereClauseItem
    {
        $this->joinEmailUser($queryBuilder);

        if (str_starts_with($folderId, 'group:')) {
            $groupFolderId = substr($folderId, 6);

            if ($groupFolderId === '') {
                $groupFolderId = null;
            }

            return WhereClause::fromRaw([
                'groupFolderId' => $groupFolderId,
                'OR' => [
                    'emailUser.id' => null,
                    'emailUser.inTrash' => false,
                ]
            ]);
        }

        return WhereClause::fromRaw([
            'emailUser.inTrash' => false,
            'emailUser.folderId' => $folderId,
            'groupFolderId' => null,
        ]);
    }

    protected function joinEmailUser(QueryBuilder $queryBuilder): void
    {
        $this->joinHelper->joinEmailUser($queryBuilder, $this->user->getId());
    }

    
    protected function getEmailAddressIdList(): array
    {
        $emailAddressList = $this->entityManager
            ->getRDBRepository(User::ENTITY_TYPE)
            ->getRelation($this->user, 'emailAddresses')
            ->select(['id'])
            ->find();

        $emailAddressIdList = [];

        foreach ($emailAddressList as $emailAddress) {
            $emailAddressIdList[] = $emailAddress->getId();
        }

        return $emailAddressIdList;
    }
}
