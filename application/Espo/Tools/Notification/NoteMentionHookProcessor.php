<?php


namespace Espo\Tools\Notification;

use Espo\Core\Acl;
use Espo\Core\AclManager;

use Espo\ORM\EntityManager;

use Espo\Entities\User;
use Espo\Entities\Note;

use stdClass;

class NoteMentionHookProcessor
{
    private $service;

    private $entityManager;

    private $user;

    private $acl;

    private $aclManager;

    public function __construct(
        Service $service,
        EntityManager $entityManager,
        User $user,
        Acl $acl,
        AclManager $aclManager
    ) {
        $this->service = $service;
        $this->entityManager = $entityManager;
        $this->user = $user;
        $this->acl = $acl;
        $this->aclManager = $aclManager;
    }

    public function beforeSave(Note $note): void
    {
        if ($note->getType() !== Note::TYPE_POST) {
            return;
        }

        $this->process($note);
    }

    private function process(Note $note): void
    {
        $post = $note->getPost() ?? '';

        $mentionData = (object) [];

        $previousMentionList = [];

        if (!$note->isNew()) {
            $previousMentionList = array_keys(
                get_object_vars($note->getData()->mentions ?? (object) [])
            );
        }

        $matches = null;

        preg_match_all('/(@[\w@.-]+)/', $post, $matches);

        $mentionCount = 0;

        if (is_array($matches) && !empty($matches[0]) && is_array($matches[0])) {
            $mentionCount = $this->processMatches($matches[0], $note, $mentionData, $previousMentionList);
        }

        $data = $note->getData();

        if ($mentionCount) {
            $data->mentions = $mentionData;
        }
        else {
            unset($data->mentions);
        }

        $note->set('data', $data);
    }

    
    private function processMatches(
        array $matchList,
        Note $note,
        stdClass $mentionData,
        array $previousMentionList
    ): int {

        $mentionCount = 0;

        $parent = $note->getParentId() && $note->getParentType() ?
            $this->entityManager->getEntity(
                $note->getParentType(),
                $note->getParentId()
            ) :
            null;

        foreach ($matchList as $item) {
            $userName = substr($item, 1);

            
            $user = $this->entityManager
                ->getRDBRepository(User::ENTITY_TYPE)
                ->where([
                    'userName' => $userName,
                    'isActive' => true,
                ])
                ->findOne();

            if (!$user) {
                continue;
            }

            if (!$this->acl->checkUserPermission($user, 'assignment')) {
                continue;
            }

            $mentionData->$item = (object) [
                'id' => $user->getId(),
                'name' => $user->get('name'),
                'userName' => $user->get('userName'),
                '_scope' => $user->getEntityType(),
            ];

            $mentionCount++;

            if (in_array($item, $previousMentionList)) {
                continue;
            }

            if ($user->getId() === $this->user->getId()) {
                continue;
            }

            if ($user->isPortal()) {
                continue;
            }

            if ($parent && !$this->aclManager->checkEntityStream($user, $parent)) {
                continue;
            }

            $note->addNotifiedUserId($user->getId());

            $this->service->notifyAboutMentionInPost($user->getId(), $note);
        }

        return $mentionCount;
    }
}
