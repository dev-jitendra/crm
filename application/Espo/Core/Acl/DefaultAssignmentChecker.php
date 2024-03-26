<?php


namespace Espo\Core\Acl;

use Espo\Core\ORM\Entity as CoreEntity;
use Espo\Repositories\User as UserRepository;
use Espo\ORM\Defs;
use Espo\ORM\Entity;
use Espo\ORM\EntityManager;
use Espo\Entities\User;
use Espo\Core\AclManager;


class DefaultAssignmentChecker implements AssignmentChecker
{
    protected const FIELD_ASSIGNED_USERS = 'assignedUsers';
    protected const FIELD_TEAMS = 'teams';
    protected const ATTR_ASSIGNED_USER_ID = 'assignedUserId';
    protected const ATTR_ASSIGNED_USERS_IDS = 'assignedUsersIds';

    public function __construct(
        private AclManager $aclManager,
        private EntityManager $entityManager,
        private Defs $ormDefs
    ) {}

    public function check(User $user, Entity $entity): bool
    {
        if (!$this->isPermittedAssignedUser($user, $entity)) {
            return false;
        }

        if (!$this->isPermittedTeams($user, $entity)) {
            return false;
        }

        if ($this->hasAssignedUsersField($entity->getEntityType())) {
            if (!$this->isPermittedAssignedUsers($user, $entity)) {
                return false;
            }
        }

        return true;
    }

    private function hasAssignedUsersField(string $entityType): bool
    {
        $entityDefs = $this->ormDefs->getEntity($entityType);

        return
            $entityDefs->hasField(self::FIELD_ASSIGNED_USERS) &&
            $entityDefs->getField(self::FIELD_ASSIGNED_USERS)->getType() === 'linkMultiple' &&
            $entityDefs->hasRelation(self::FIELD_ASSIGNED_USERS) &&
            $entityDefs->getRelation(self::FIELD_ASSIGNED_USERS)->getForeignEntityType() === 'User';
    }

    protected function isPermittedAssignedUser(User $user, Entity $entity): bool
    {
        if (!$entity->hasAttribute(self::ATTR_ASSIGNED_USER_ID)) {
            return true;
        }

        $assignedUserId = $entity->get(self::ATTR_ASSIGNED_USER_ID);

        if ($user->isPortal()) {
            if (!$entity->isAttributeChanged(self::ATTR_ASSIGNED_USER_ID)) {
                return true;
            }

            return false;
        }

        $assignmentPermission = $this->aclManager->getPermissionLevel($user, 'assignmentPermission');

        if (
            $assignmentPermission === Table::LEVEL_YES ||
            !in_array($assignmentPermission, [Table::LEVEL_TEAM, Table::LEVEL_NO])
        ) {
            return true;
        }

        $toProcess = false;

        if (!$entity->isNew()) {
            if ($entity->isAttributeChanged(self::ATTR_ASSIGNED_USER_ID)) {
                $toProcess = true;
            }
        }
        else {
            $toProcess = true;
        }

        if (!$toProcess) {
            return true;
        }

        if (empty($assignedUserId)) {
            if ($assignmentPermission === Table::LEVEL_NO && !$user->isApi()) {
                return false;
            }

            return true;
        }

        if ($assignmentPermission === Table::LEVEL_NO) {
            if ($user->getId() !== $assignedUserId) {
                return false;
            }
        }
        else if ($assignmentPermission === Table::LEVEL_TEAM) {
            $teamIdList = $user->getTeamIdList();

            if (
                !$this->getUserRepository()->checkBelongsToAnyOfTeams($assignedUserId, $teamIdList)
            ) {
                return false;
            }
        }

        return true;
    }

    private function getUserRepository(): UserRepository
    {
        
        return $this->entityManager->getRepository('User');
    }

    protected function isPermittedTeams(User $user, Entity $entity): bool
    {
        $assignmentPermission = $this->aclManager->getPermissionLevel($user, 'assignmentPermission');

        if (!in_array($assignmentPermission, [Table::LEVEL_TEAM, Table::LEVEL_NO])) {
            return true;
        }

        if (!$entity instanceof CoreEntity) {
            return true;
        }

        if (!$entity->hasLinkMultipleField(self::FIELD_TEAMS)) {
            return true;
        }

        $teamIdList = $entity->getLinkMultipleIdList(self::FIELD_TEAMS);

        if (empty($teamIdList)) {
            return $this->isPermittedTeamsEmpty($user, $entity);
        }

        $newIdList = [];

        if (!$entity->isNew()) {
            $existingIdList = [];

            $teamCollection = $this->entityManager
                ->getRDBRepository($entity->getEntityType())
                ->getRelation($entity, self::FIELD_TEAMS)
                ->select('id')
                ->find();

            foreach ($teamCollection as $team) {
                $existingIdList[] = $team->getId();
            }

            foreach ($teamIdList as $id) {
                if (!in_array($id, $existingIdList)) {
                    $newIdList[] = $id;
                }
            }
        }
        else {
            $newIdList = $teamIdList;
        }

        if (empty($newIdList)) {
            return true;
        }

        $userTeamIdList = $user->getLinkMultipleIdList(self::FIELD_TEAMS);

        foreach ($newIdList as $id) {
            if (!in_array($id, $userTeamIdList)) {
                return false;
            }
        }

        return true;
    }

    private function isPermittedTeamsEmpty(User $user, CoreEntity $entity): bool
    {
        $assignmentPermission = $this->aclManager->getPermissionLevel($user, 'assignmentPermission');

        if ($assignmentPermission !== Table::LEVEL_TEAM) {
            return true;
        }

        if ($entity->hasLinkMultipleField(self::FIELD_ASSIGNED_USERS)) {
            $assignedUserIdList = $entity->getLinkMultipleIdList(self::FIELD_ASSIGNED_USERS);

            if (empty($assignedUserIdList)) {
                return false;
            }
        }
        else if ($entity->hasAttribute(self::ATTR_ASSIGNED_USER_ID)) {
            if (!$entity->get(self::ATTR_ASSIGNED_USER_ID)) {
                return false;
            }
        }

        return true;
    }

    protected function isPermittedAssignedUsers(User $user, Entity $entity): bool
    {
        if (!$entity instanceof CoreEntity) {
            return true;
        }

        if (!$entity->hasLinkMultipleField(self::FIELD_ASSIGNED_USERS)) {
            return true;
        }

        if ($user->isPortal()) {
            if (!$entity->isAttributeChanged(self::ATTR_ASSIGNED_USERS_IDS)) {
                return true;
            }

            return false;
        }

        $assignmentPermission = $this->aclManager->getPermissionLevel($user, 'assignmentPermission');

        if (
            $assignmentPermission === Table::LEVEL_YES ||
            !in_array($assignmentPermission, [Table::LEVEL_TEAM, Table::LEVEL_NO])
        ) {
            return true;
        }

        $toProcess = false;

        if (!$entity->isNew()) {
            
            $entity->getLinkMultipleIdList(self::FIELD_ASSIGNED_USERS);

            if ($entity->isAttributeChanged(self::ATTR_ASSIGNED_USERS_IDS)) {
                $toProcess = true;
            }
        }
        else {
            $toProcess = true;
        }

        $userIdList = $entity->getLinkMultipleIdList(self::FIELD_ASSIGNED_USERS);

        if (!$toProcess) {
            return true;
        }

        if (empty($userIdList)) {
            if ($assignmentPermission === Table::LEVEL_NO && !$user->isApi()) {
                return false;
            }

            return true;
        }

        if ($assignmentPermission === Table::LEVEL_NO) {
            return $this->isPermittedAssignedUsersLevelNo($user, $entity);
        }

        if ($assignmentPermission === Table::LEVEL_TEAM) {
            return $this->isPermittedAssignedUsersLevelTeam($user, $entity);
        }

        return true;
    }

    private function isPermittedAssignedUsersLevelNo(User $user, CoreEntity $entity): bool
    {
        $userIdList = $entity->getLinkMultipleIdList(self::FIELD_ASSIGNED_USERS);

        $fetchedAssignedUserIdList = $entity->getFetched(self::ATTR_ASSIGNED_USERS_IDS);

        foreach ($userIdList as $userId) {
            if (!$entity->isNew() && in_array($userId, $fetchedAssignedUserIdList)) {
                continue;
            }

            if ($user->getId() !== $userId) {
                return false;
            }
        }

        return true;
    }

    private function isPermittedAssignedUsersLevelTeam(User $user, CoreEntity $entity): bool
    {
        $userIdList = $entity->getLinkMultipleIdList(self::FIELD_ASSIGNED_USERS);

        $fetchedAssignedUserIdList = $entity->getFetched(self::ATTR_ASSIGNED_USERS_IDS);

        $teamIdList = $user->getLinkMultipleIdList(self::FIELD_TEAMS);

        foreach ($userIdList as $userId) {
            if (!$entity->isNew() && in_array($userId, $fetchedAssignedUserIdList)) {
                continue;
            }

            if (
                !$this->getUserRepository()->checkBelongsToAnyOfTeams($userId, $teamIdList)
            ) {
                return false;
            }
        }

        return true;
    }
}
