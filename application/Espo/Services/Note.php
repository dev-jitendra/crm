<?php


namespace Espo\Services;

use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\ForbiddenSilent;

use Espo\Repositories\User as UserRepository;

use Espo\Core\Acl\Table as AclTable;

use Espo\Entities\Preferences;
use Espo\Entities\Note as NoteEntity;
use Espo\Entities\User as UserEntity;

use Espo\ORM\Entity;

use stdClass;


class Note extends Record
{
    protected function afterCreateEntity(Entity $entity, $data)
    {
        parent::afterCreateEntity($entity, $data);

        

        $this->processFollowAfterCreate($entity);
    }

    protected function processFollowAfterCreate(NoteEntity $entity): void
    {
        $parentType = $entity->getParentType();
        $parentId = $entity->getParentId();

        if (
            $entity->getType() !== NoteEntity::TYPE_POST ||
            !$parentType ||
            !$parentId
        ) {
            return;
        }

        if (!$this->metadata->get(['scopes', $parentType, 'stream'])) {
            return;
        }

        $preferences = $this->entityManager->getEntityById(Preferences::ENTITY_TYPE, $this->user->getId());

        if (!$preferences) {
            return;
        }

        if (!$preferences->get('followEntityOnStreamPost')) {
            return;
        }

        $parent = $this->entityManager->getEntityById($parentType, $parentId);

        if (!$parent || $this->user->isSystem() || $this->user->isApi()) {
            return;
        }

        $this->getStreamService()->followEntity($parent, $this->user->getId());
    }

    
    protected function beforeCreateEntity(Entity $entity, $data)
    {
        $parentType = $data->parentType ?? null;
        $parentId = $data->parentId ?? null;

        if ($parentType && $parentId) {
            $parent = $this->entityManager->getEntity($data->parentType, $data->parentId);

            if ($parent && !$this->acl->check($parent, AclTable::ACTION_READ)) {
                throw new Forbidden();
            }
        }

        parent::beforeCreateEntity($entity, $data);

        if (!$entity->isPost() && !$this->user->isAdmin()) {
            throw new ForbiddenSilent("Only 'Post' type allowed.");
        }

        if ($this->user->isPortal()) {
            $entity->set('isInternal', false);
        }

        if ($entity->isPost()) {
            $this->handlePostText($entity);
        }

        $targetType = $entity->getTargetType();

        $entity->clear('isGlobal');

        switch ($targetType) {
            case NoteEntity::TARGET_ALL:

                $entity->clear('usersIds');
                $entity->clear('teamsIds');
                $entity->clear('portalsIds');
                $entity->set('isGlobal', true);

                break;

            case NoteEntity::TARGET_SELF:

                $entity->clear('usersIds');
                $entity->clear('teamsIds');
                $entity->clear('portalsIds');
                $entity->set('usersIds', [$this->user->getId()]);
                $entity->set('isForSelf', true);

                break;

            case NoteEntity::TARGET_USERS:

                $entity->clear('teamsIds');
                $entity->clear('portalsIds');

                break;

            case NoteEntity::TARGET_TEAMS:

                $entity->clear('usersIds');
                $entity->clear('portalsIds');

                break;

            case NoteEntity::TARGET_PORTALS:

                $entity->clear('usersIds');
                $entity->clear('teamsIds');

                break;
        }
    }

    public function filterUpdateInput(stdClass $data): void
    {
        parent::filterUpdateInput($data);

        unset($data->parentId);
        unset($data->parentType);
        unset($data->targetType);
        unset($data->usersIds);
        unset($data->teamsIds);
        unset($data->portalsIds);
        unset($data->isGlobal);
    }

    
    protected function beforeUpdateEntity(Entity $entity, $data)
    {
        parent::beforeUpdateEntity($entity, $data);

        if ($entity->isPost()) {
            $this->handlePostText($entity);
        }

        if (!$entity->isPost() && !$this->user->isAdmin()) {
            throw new ForbiddenSilent("Only 'Post' type allowed.");
        }
    }

    protected function handlePostText(NoteEntity $entity): void
    {
        $post = $entity->getPost();

        if (empty($post)) {
            return;
        }

        $siteUrl = $this->config->getSiteUrl();

        $regexp = '/' . preg_quote($siteUrl, '/') .
            '(\/portal|\/portal\/[a-zA-Z0-9]*)?\/#([A-Z][a-zA-Z0-9]*)\/view\/([a-zA-Z0-9-]*)/';

        $post = preg_replace($regexp, '[\2/\3](#\2/view/\3)', $post);

        $entity->set('post', $post);
    }

    
    protected function processAssignmentCheck(Entity $entity): void
    {
        

        if (!$entity->isNew()) {
            return;
        }

        $targetType = $entity->getTargetType();

        if (!$targetType) {
            return;
        }

        $userTeamIdList = $this->user->getTeamIdList();

        
        $userIdList = $entity->getLinkMultipleIdList('users');
        
        $portalIdList = $entity->getLinkMultipleIdList('portals');
        
        $teamIdList = $entity->getLinkMultipleIdList('teams');

        
        $targetUserList = [];

        if ($targetType === NoteEntity::TARGET_USERS) {
            
            $targetUserList = $this->entityManager
                ->getRDBRepository(UserEntity::ENTITY_TYPE)
                ->select(['id', 'type'])
                ->where([
                    'id' => $userIdList,
                ])
                ->find();
        }

        $hasPortalTargetUser = false;
        $allTargetUsersArePortal = true;

        foreach ($targetUserList as $user) {
            if (!$user->isPortal()) {
                $allTargetUsersArePortal = false;
            }

            if ($user->isPortal()) {
                $hasPortalTargetUser = true;
            }
        }

        $messagePermission = $this->acl->getPermissionLevel('message');

        if ($messagePermission === AclTable::LEVEL_NO) {
            if (
                $targetType !== NoteEntity::TARGET_SELF &&
                $targetType !== NoteEntity::TARGET_PORTALS &&
                !(
                    $targetType === NoteEntity::TARGET_USERS &&
                    count($userIdList) === 1 &&
                    $userIdList[0] === $this->user->getId()
                ) &&
                !(
                    $targetType === NoteEntity::TARGET_USERS && $allTargetUsersArePortal
                )
            ) {
                throw new Forbidden('Not permitted to post to anybody except self.');
            }
        }

        if ($targetType === NoteEntity::TARGET_TEAMS) {
            if (empty($teamIdList)) {
                throw new BadRequest("No team IDS.");
            }
        }

        if ($targetType === NoteEntity::TARGET_USERS) {
            if (empty($userIdList)) {
                throw new BadRequest("No user IDs.");
            }
        }

        if ($targetType === NoteEntity::TARGET_PORTALS) {
            if (empty($portalIdList)) {
                throw new BadRequest("No portal IDs.");
            }

            if ($this->acl->getPermissionLevel('portal') !== AclTable::LEVEL_YES) {
                throw new Forbidden('Not permitted to post to portal users.');
            }
        }

        if (
            $targetType === NoteEntity::TARGET_USERS &&
            $this->acl->getPermissionLevel('portal') !== AclTable::LEVEL_YES
        ) {
            if ($hasPortalTargetUser) {
                throw new Forbidden('Not permitted to post to portal users.');
            }
        }

        if ($messagePermission === AclTable::LEVEL_TEAM) {
            if ($targetType === NoteEntity::TARGET_ALL) {
                throw new Forbidden('Not permitted to post to all.');
            }
        }

        if (
            $messagePermission === AclTable::LEVEL_TEAM &&
            $targetType === NoteEntity::TARGET_TEAMS
        ) {
            if (empty($userTeamIdList)) {
                throw new Forbidden('Not permitted to post to foreign teams.');
            }

            foreach ($teamIdList as $teamId) {
                if (!in_array($teamId, $userTeamIdList)) {
                    throw new Forbidden("Not permitted to post to foreign teams.");
                }
            }
        }

        if (
            $messagePermission === AclTable::LEVEL_TEAM &&
            $targetType === NoteEntity::TARGET_USERS
        ) {
            if (empty($userTeamIdList)) {
                throw new Forbidden('Not permitted to post to users from foreign teams.');
            }

            foreach ($targetUserList as $user) {
                if ($user->getId() === $this->user->getId()) {
                    continue;
                }

                if ($user->isPortal()) {
                    continue;
                }

                $inTeam = $this->getUserRepository()->checkBelongsToAnyOfTeams($user->getId(), $userTeamIdList);

                if (!$inTeam) {
                    throw new Forbidden('Not permitted to post to users from foreign teams.');
                }
            }
        }
    }

    public function link(string $id, string $link, string $foreignId) : void
    {
        if ($link === 'teams' || $link === 'users') {
            throw new Forbidden();
        }

        parent::link($id, $link, $foreignId);
    }

    public function unlink(string $id, string $link, string $foreignId) : void
    {
        if ($link === 'teams' || $link === 'users') {
            throw new Forbidden();
        }

        parent::unlink($id, $link, $foreignId);
    }

    
    public function loadAdditionalFields(Entity $entity)
    {
        parent::loadAdditionalFields($entity);

        $entity->loadAdditionalFields();
    }

    private function getUserRepository(): UserRepository
    {
        
        return $this->entityManager->getRepository(UserEntity::ENTITY_TYPE);
    }
}
