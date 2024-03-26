<?php


namespace Espo\Classes\Acl\Note;

use Espo\Entities\Note;
use Espo\Entities\User;
use Espo\ORM\Entity;
use Espo\Core\Acl\AccessEntityCREDChecker;
use Espo\Core\Acl\DefaultAccessChecker;
use Espo\Core\Acl\ScopeData;
use Espo\Core\Acl\Traits\DefaultAccessCheckerDependency;
use Espo\Core\AclManager;
use Espo\Core\ORM\EntityManager;
use Espo\Core\Utils\Config;

use DateTime;
use Exception;


class AccessChecker implements AccessEntityCREDChecker
{
    use DefaultAccessCheckerDependency;

    private const EDIT_PERIOD = '7 days';
    private const DELETE_PERIOD = '1 month';

    private DefaultAccessChecker $defaultAccessChecker;
    private AclManager $aclManager;
    private EntityManager $entityManager;
    private Config $config;

    public function __construct(
        DefaultAccessChecker $defaultAccessChecker,
        AclManager $aclManager,
        EntityManager $entityManager,
        Config $config
    ) {
        $this->defaultAccessChecker = $defaultAccessChecker;
        $this->aclManager = $aclManager;
        $this->entityManager = $entityManager;
        $this->config = $config;
    }

    
    public function checkEntityCreate(User $user, Entity $entity, ScopeData $data): bool
    {
        $parentId = $entity->get('parentId');
        $parentType = $entity->get('parentType');

        if (!$parentId || !$parentType) {
            return true;
        }

        $parent = $this->entityManager->getEntity($parentType, $parentId);

        if ($parent && $this->aclManager->checkEntityStream($user, $parent)) {
            return true;
        }

        return false;
    }

    
    public function checkEntityRead(User $user, Entity $entity, ScopeData $data): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        $parentId = $entity->getParentId();
        $parentType = $entity->getParentType();

        if ($parentId && $parentType) {
            $parent = $this->entityManager->getEntityById($parentType, $parentId);

            if (!$parent) {
                return false;
            }

            return $this->aclManager->checkEntityStream($user, $parent);
        }

        if ($entity->getType() !== Note::TYPE_POST) {
            return false;
        }

        if ($entity->getCreatedById() === $user->getId()) {
            return true;
        }

        if ($entity->getTargetType() === Note::TARGET_ALL) {
            return true;
        }

        if ($entity->getTargetType() === Note::TARGET_TEAMS) {
            $targetTeamIdList = $entity->getLinkMultipleIdList('teams');

            foreach ($user->getTeamIdList() as $teamId) {
                if (in_array($teamId, $targetTeamIdList)) {
                    return true;
                }
            }

            return false;
        }

        if ($entity->getTargetType() === Note::TARGET_USERS) {
            return in_array($user->getId(), $entity->getLinkMultipleIdList('users'));
        }

        return false;
    }

    
    public function checkEntityEdit(User $user, Entity $entity, ScopeData $data): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if (!$this->defaultAccessChecker->checkEntityEdit($user, $entity, $data)) {
            return false;
        }

        if (!$this->aclManager->checkOwnershipOwn($user, $entity)) {
            return false;
        }

        $createdAt = $entity->get('createdAt');

        if (!$createdAt) {
            return true;
        }

        $noteEditThresholdPeriod =
            '-' .  $this->config->get('noteEditThresholdPeriod', self::EDIT_PERIOD);

        $dt = new DateTime();

        $dt->modify($noteEditThresholdPeriod);

        try {
            if ($dt->format('U') > (new DateTime($createdAt))->format('U')) {
                return false;
            }
        }
        catch (Exception $e) {
            return false;
        }

        return true;
    }

    
    public function checkEntityDelete(User $user, Entity $entity, ScopeData $data): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if (!$this->defaultAccessChecker->checkEntityDelete($user, $entity, $data)) {
            return false;
        }

        if (!$this->aclManager->checkOwnershipOwn($user, $entity)) {
            return false;
        }

        $createdAt = $entity->get('createdAt');

        if (!$createdAt) {
            return true;
        }

        $deleteThresholdPeriod =
            '-' . $this->config->get('noteDeleteThresholdPeriod', self::DELETE_PERIOD);

        $dt = new DateTime();

        $dt->modify($deleteThresholdPeriod);

        try {
            if ($dt->format('U') > (new DateTime($createdAt))->format('U')) {
                return false;
            }
        }
        catch (Exception $e) {
            return false;
        }

        return true;
    }
}
