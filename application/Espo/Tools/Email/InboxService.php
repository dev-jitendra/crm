<?php


namespace Espo\Tools\Email;

use Espo\Core\Acl\Table;
use Espo\Core\AclManager;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Exceptions\NotFound;
use Espo\Core\Select\SelectBuilderFactory;
use Espo\Core\Select\Where\Item as WhereItem;
use Espo\Core\Utils\Log;
use Espo\Entities\Email;
use Espo\Entities\EmailFolder;
use Espo\Entities\GroupEmailFolder;
use Espo\Entities\Notification;
use Espo\Entities\User;
use Espo\ORM\EntityManager;
use Exception;

class InboxService
{
    private const FOLDER_INBOX = Folder::INBOX;
    private const FOLDER_DRAFTS = Folder::DRAFTS;

    public function __construct(
        private User $user,
        private EntityManager $entityManager,
        private AclManager $aclManager,
        private Log $log,
        private SelectBuilderFactory $selectBuilderFactory
    ) {}

    
    public function moveToFolderIdList(array $idList, ?string $folderId, ?string $userId = null): void
    {
        foreach ($idList as $id) {
            try {
                $this->moveToFolder($id, $folderId, $userId);
            }
            catch (Exception) {}
        }
    }

    
    public function moveToFolder(string $id, ?string $folderId, ?string $userId = null): void
    {
        $userId = $userId ?? $this->user->getId();

        if ($folderId === self::FOLDER_INBOX) {
            $folderId = null;
        }

        $email = $this->entityManager->getRDBRepositoryByClass(Email::class)->getById($id);

        if (!$email) {
            throw new NotFound();
        }

        $user = $userId === $this->user->getId() ?
            $this->user :
            $this->entityManager
                ->getRDBRepositoryByClass(User::class)
                ->getById($userId);

        if (!$user) {
            throw new NotFound("User not found.");
        }

        $previousFolderLink = $email->getGroupFolder();

        if ($previousFolderLink) {
            $previousFolderId = $previousFolderLink->getId();

            $previousFolder = $this->entityManager->getEntityById(GroupEmailFolder::ENTITY_TYPE, $previousFolderId);

            if ($previousFolder && !$this->aclManager->checkEntityRead($user, $previousFolder)) {
                throw new Forbidden("No access to current group folder.");
            }

            if (
                in_array(
                    'groupFolder',
                    $this->aclManager->getScopeForbiddenFieldList($user, Email::ENTITY_TYPE, Table::ACTION_EDIT)
                )
            ) {
                throw new Forbidden("No access to `groupFolder` field.");
            }
        }

        if ($folderId && strpos($folderId, 'group:') === 0) {
            try {
                $this->moveToGroupFolder($email, substr($folderId, 6), $user);
            }
            catch (Exception $e) {
                $this->log->debug("Move to group folder exception: " . $e->getMessage());

                throw $e;
            }

            return;
        }

        if ($previousFolderLink) {
            $email->set('groupFolderId', null);

            if (!$this->aclManager->checkEntityRead($user, $email)) {
                throw new Forbidden("No read access to email to unset group folder.");
            }

            $this->entityManager->saveEntity($email);
        }

        $update = $this->entityManager
            ->getQueryBuilder()
            ->update()
            ->in(Email::RELATIONSHIP_EMAIL_USER)
            ->set([
                'folderId' => $folderId,
                'inTrash' => false,
            ])
            ->where([
                'deleted' => false,
                'userId' => $userId,
                'emailId' => $id,
            ])
            ->build();

        $this->entityManager->getQueryExecutor()->execute($update);
    }

    
    private function moveToGroupFolder(Email $email, string $folderId, User $user): void
    {
        $folder = $this->entityManager->getEntityById(GroupEmailFolder::ENTITY_TYPE, $folderId);

        if (!$folder) {
            throw new NotFound("Group folder not found.");
        }

        if (!$this->aclManager->checkEntityRead($user, $folder)) {
            throw new Forbidden("No access to folder.");
        }

        if (!$this->aclManager->checkEntityRead($user, $email)) {
            throw new Forbidden("No read access to email to unset group folder.");
        }

        if (
            in_array(
                'groupFolder',
                $this->aclManager->getScopeForbiddenFieldList($user, Email::ENTITY_TYPE, Table::ACTION_EDIT)
            )
        ) {
            throw new Forbidden("No access to `groupFolder` field.");
        }

        $email->set('groupFolderId', $folderId);

        $this->entityManager->saveEntity($email);
    }

    
    public function moveToTrashIdList(array $idList, ?string $userId = null): bool
    {
        foreach ($idList as $id) {
            $this->moveToTrash($id, $userId);
        }

        return true;
    }

    
    public function retrieveFromTrashIdList(array $idList, ?string $userId = null): void
    {
        foreach ($idList as $id) {
            $this->retrieveFromTrash($id, $userId);
        }
    }

    public function moveToTrash(string $id, ?string $userId = null): void
    {
        $userId = $userId ?? $this->user->getId();

        $update = $this->entityManager
            ->getQueryBuilder()
            ->update()
            ->in(Email::RELATIONSHIP_EMAIL_USER)
            ->set(['inTrash' => true])
            ->where([
                'deleted' => false,
                'userId' => $userId,
                'emailId' => $id,
            ])
            ->build();

        $this->entityManager->getQueryExecutor()->execute($update);

        $this->markNotificationAsRead($id, $userId);
    }

    public function retrieveFromTrash(string $id, ?string $userId = null): void
    {
        $userId = $userId ?? $this->user->getId();

        $update = $this->entityManager
            ->getQueryBuilder()
            ->update()
            ->in(Email::RELATIONSHIP_EMAIL_USER)
            ->set(['inTrash' => false])
            ->where([
                'deleted' => false,
                'userId' => $userId,
                'emailId' => $id,
            ])
            ->build();

        $this->entityManager->getQueryExecutor()->execute($update);
    }

    
    public function markAsReadIdList(array $idList, ?string $userId = null): void
    {
        foreach ($idList as $id) {
            $this->markAsRead($id, $userId);
        }
    }

    
    public function markAsNotReadIdList(array $idList, ?string $userId = null): void
    {
        foreach ($idList as $id) {
            $this->markAsNotRead($id, $userId);
        }
    }

    public function markAsRead(string $id, ?string $userId = null): void
    {
        $userId = $userId ?? $this->user->getId();

        $update = $this->entityManager
            ->getQueryBuilder()
            ->update()
            ->in(Email::RELATIONSHIP_EMAIL_USER)
            ->set(['isRead' => true])
            ->where([
                'deleted' => false,
                'userId' => $userId,
                'emailId' => $id,
            ])
            ->build();

        $this->entityManager->getQueryExecutor()->execute($update);

        $this->markNotificationAsRead($id, $userId);
    }

    public function markAsNotRead(string $id, ?string $userId = null): void
    {
        $userId = $userId ?? $this->user->getId();

        $update = $this->entityManager
            ->getQueryBuilder()
            ->update()
            ->in(Email::RELATIONSHIP_EMAIL_USER)
            ->set(['isRead' => false])
            ->where([
                'deleted' => false,
                'userId' => $userId,
                'emailId' => $id,
            ])
            ->build();

        $this->entityManager->getQueryExecutor()->execute($update);
    }

    
    public function markAsImportantIdList(array $idList, ?string $userId = null): void
    {
        foreach ($idList as $id) {
            $this->markAsImportant($id, $userId);
        }
    }

    
    public function markAsNotImportantIdList(array $idList, ?string $userId = null): void
    {
        foreach ($idList as $id) {
            $this->markAsNotImportant($id, $userId);
        }
    }

    public function markAsImportant(string $id, ?string $userId = null): void
    {
        $userId = $userId ?? $this->user->getId();

        $update = $this->entityManager
            ->getQueryBuilder()
            ->update()
            ->in(Email::RELATIONSHIP_EMAIL_USER)
            ->set(['isImportant' => true])
            ->where([
                'deleted' => false,
                'userId' => $userId,
                'emailId' => $id,
            ])
            ->build();

        $this->entityManager->getQueryExecutor()->execute($update);
    }

    public function markAsNotImportant(string $id, ?string $userId = null): void
    {
        $userId = $userId ?? $this->user->getId();

        $update = $this->entityManager
            ->getQueryBuilder()
            ->update()
            ->in(Email::RELATIONSHIP_EMAIL_USER)
            ->set(['isImportant' => false])
            ->where([
                'deleted' => false,
                'userId' => $userId,
                'emailId' => $id,
            ])
            ->build();

        $this->entityManager->getQueryExecutor()->execute($update);
    }

    public function markAllAsRead(?string $userId = null): void
    {
        $userId = $userId ?? $this->user->getId();

        $update = $this->entityManager
            ->getQueryBuilder()
            ->update()
            ->in(Email::RELATIONSHIP_EMAIL_USER)
            ->set(['isRead' => true])
            ->where([
                'deleted' => false,
                'userId' => $userId,
                'isRead' => false,
            ])
            ->build();

        $this->entityManager->getQueryExecutor()->execute($update);

        $update = $this
            ->entityManager
            ->getQueryBuilder()
            ->update()
            ->in(Notification::ENTITY_TYPE)
            ->set(['read' => true])
            ->where([
                'deleted' => false,
                'userId' => $userId,
                'relatedType' => Email::ENTITY_TYPE,
                'read' => false,
                'type' => Notification::TYPE_EMAIL_RECEIVED,
            ])
            ->build();

        $this->entityManager->getQueryExecutor()->execute($update);
    }

    public function markNotificationAsRead(string $id, string $userId): void
    {
        $update = $this->entityManager
            ->getQueryBuilder()
            ->update()
            ->in(Notification::ENTITY_TYPE)
            ->set(['read' => true])
            ->where([
                'deleted' => false,
                'userId' => $userId,
                'relatedType' => Email::ENTITY_TYPE,
                'relatedId' => $id,
                'read' => false,
                'type' => Notification::TYPE_EMAIL_RECEIVED,
            ])
            ->build();

        $this->entityManager->getQueryExecutor()->execute($update);
    }

    
    public function getFoldersNotReadCounts(): array
    {
        $data = [];

        $selectBuilder = $this->selectBuilderFactory
            ->create()
            ->from(Email::ENTITY_TYPE)
            ->withAccessControlFilter();

        $draftsSelectBuilder = clone $selectBuilder;

        $selectBuilder->withWhere(
            WhereItem::fromRaw([
                'type' => 'isTrue',
                'attribute' => 'isNotRead',
            ])
        );

        $folderIdList = [self::FOLDER_INBOX, self::FOLDER_DRAFTS];

        $emailFolderList = $this->entityManager
            ->getRDBRepository(EmailFolder::ENTITY_TYPE)
            ->where([
                'assignedUserId' => $this->user->getId(),
            ])
            ->find();

        foreach ($emailFolderList as $folder) {
            $folderIdList[] = $folder->getId();
        }

        $groupFolderList = $this->entityManager
            ->getRDBRepositoryByClass(GroupEmailFolder::class)
            ->distinct()
            ->leftJoin('teams')
            ->where(
                $this->user->isAdmin() ?
                    ['id!=' => null] :
                    ['teams.id' => $this->user->getTeamIdList()]
            )
            ->find();

        foreach ($groupFolderList as $folder) {
            $folderIdList[] = 'group:' . $folder->getId();
        }

        foreach ($folderIdList as $folderId) {
            $itemSelectBuilder = clone $selectBuilder;

            if ($folderId === self::FOLDER_DRAFTS) {
                $itemSelectBuilder = clone $draftsSelectBuilder;
            }

            $itemSelectBuilder->withWhere(
                WhereItem::fromRaw([
                    'type' => 'inFolder',
                    'attribute' => 'folderId',
                    'value' => $folderId,
                ])
            );

            $data[$folderId] = $this->entityManager
                ->getRDBRepository(Email::ENTITY_TYPE)
                ->clone($itemSelectBuilder->build())
                ->count();
        }

        return $data;
    }
}
