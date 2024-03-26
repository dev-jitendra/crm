<?php


namespace Espo\Tools\Notification;

use Espo\Core\Utils\Id\RecordIdGenerator;
use Espo\Entities\Note;
use Espo\Entities\Notification;
use Espo\Entities\User;
use Espo\Entities\Email;

use Espo\Core\Utils\Config;
use Espo\Core\AclManager;
use Espo\Core\WebSocket\Submission;
use Espo\Core\Utils\DateTime as DateTimeUtil;

use Espo\Modules\Crm\Entities\CaseObj;
use Espo\ORM\EntityManager;

class Service
{
    public function __construct(
        private EntityManager $entityManager,
        private Config $config,
        private AclManager $aclManager,
        private Submission $webSocketSubmission,
        private RecordIdGenerator $idGenerator
    ) {}

    public function notifyAboutMentionInPost(string $userId, Note $note): void
    {
        $this->entityManager->createEntity(Notification::ENTITY_TYPE, [
            'type' => Notification::TYPE_MENTION_IN_POST,
            'data' => [
                'noteId' => $note->getId(),
            ],
            'userId' => $userId,
            'relatedId' => $note->getId(),
            'relatedType' => Note::ENTITY_TYPE,
        ]);
    }

    
    public function notifyAboutNote(array $userIdList, Note $note): void
    {
        $related = null;

        if ($note->getRelatedType() === Email::ENTITY_TYPE) {
            $related = $this->entityManager
                ->getRDBRepository(Email::ENTITY_TYPE)
                ->select(['id', 'sentById', 'createdById'])
                ->where(['id' => $note->getRelatedId()])
                ->findOne();
        }

        $now = date(DateTimeUtil::SYSTEM_DATE_TIME_FORMAT);

        $collection = $this->entityManager
            ->getCollectionFactory()
            ->create();

        $userList = $this->entityManager
            ->getRDBRepository(User::ENTITY_TYPE)
            ->select(['id', 'type'])
            ->where([
                'isActive' => true,
                'id' => $userIdList,
            ])
            ->find();

        foreach ($userList as $user) {
            $userId = $user->getId();

            if (!$this->checkUserNoteAccess($user, $note)) {
                continue;
            }

            if ($note->get('createdById') === $user->getId()) {
                continue;
            }

            if (
                $related &&
                $related->getEntityType() === Email::ENTITY_TYPE &&
                $related->get('sentById') === $user->getId()
            ) {
                continue;
            }

            if ($related && $related->get('createdById') === $user->getId()) {
                continue;
            }

            $notification = $this->entityManager->getNewEntity(Notification::ENTITY_TYPE);

            $notification->set([
                'id' => $this->idGenerator->generate(),
                'data' => [
                    'noteId' => $note->getId(),
                ],
                'type' => Notification::TYPE_NOTE,
                'userId' => $userId,
                'createdAt' => $now,
                'relatedId' => $note->getId(),
                'relatedType' => Note::ENTITY_TYPE,
                'relatedParentId' => $note->getParentId(),
                'relatedParentType' => $note->getParentType(),
            ]);

            $collection[] = $notification;
        }

        if (!count($collection)) {
            return;
        }

        $this->entityManager->getMapper()->massInsert($collection);

        if ($this->config->get('useWebSocket')) {
            foreach ($userIdList as $userId) {
                $this->webSocketSubmission->submit('newNotification', $userId);
            }
        }
    }

    private function checkUserNoteAccess(User $user, Note $note): bool
    {
        if ($user->isPortal()) {
            if ($note->getRelatedType()) {
                
                return
                    $note->getRelatedType() === Email::ENTITY_TYPE &&
                    $note->getParentType() === CaseObj::ENTITY_TYPE;
            }

            return true;
        }

        if ($note->getRelatedType()) {
            if (!$this->aclManager->checkScope($user, $note->getRelatedType())) {
                return false;
            }
        }

        if ($note->getParentType()) {
            if (!$this->aclManager->checkScope($user, $note->getParentType())) {
                return false;
            }
        }

        return true;
    }
}
