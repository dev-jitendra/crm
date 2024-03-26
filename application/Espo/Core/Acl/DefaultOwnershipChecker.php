<?php


namespace Espo\Core\Acl;

use Espo\ORM\Entity;
use Espo\Core\ORM\Entity as CoreEntity;
use Espo\Entities\User;


class DefaultOwnershipChecker implements OwnershipOwnChecker, OwnershipTeamChecker
{
    private const ATTR_CREATED_BY_ID = 'createdById';
    private const ATTR_ASSIGNED_USER_ID = 'assignedUserId';
    private const ATTR_ASSIGNED_TEAMS_IDS = 'teamsIds';
    private const FIELD_TEAMS = 'teams';
    private const FIELD_ASSIGNED_USERS = 'assignedUsers';

    public function checkOwn(User $user, Entity $entity): bool
    {
        if ($entity->hasAttribute(self::ATTR_ASSIGNED_USER_ID)) {
            if (
                $entity->has(self::ATTR_ASSIGNED_USER_ID) &&
                $user->getId() === $entity->get(self::ATTR_ASSIGNED_USER_ID)
            ) {
                return true;
            }
        }
        else if ($entity->hasAttribute(self::ATTR_CREATED_BY_ID)) {
            if (
                $entity->has(self::ATTR_CREATED_BY_ID) &&
                $user->getId() === $entity->get(self::ATTR_CREATED_BY_ID)
            ) {
                return true;
            }
        }

        if ($entity instanceof CoreEntity && $entity->hasLinkMultipleField(self::FIELD_ASSIGNED_USERS)) {
            if ($entity->hasLinkMultipleId(self::FIELD_ASSIGNED_USERS, $user->getId())) {
                return true;
            }
        }

        return false;
    }

    public function checkTeam(User $user, Entity $entity): bool
    {
        if (!$entity instanceof CoreEntity) {
            return false;
        }

        
        $userTeamIdList = $user->getLinkMultipleIdList(self::FIELD_TEAMS);

        if (
            !$entity->hasRelation(self::FIELD_TEAMS) ||
            !$entity->hasAttribute(self::ATTR_ASSIGNED_TEAMS_IDS)
        ) {
            return false;
        }

        $entityTeamIdList = $entity->getLinkMultipleIdList(self::FIELD_TEAMS);

        if (empty($entityTeamIdList)) {
            return false;
        }

        foreach ($userTeamIdList as $id) {
            if (in_array($id, $entityTeamIdList)) {
                return true;
            }
        }

        return false;
    }
}
