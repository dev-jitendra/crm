<?php


namespace Espo\Modules\Crm\Services;

use Espo\ORM\Entity;
use Espo\Services\Record;
use Espo\Core\ORM\Entity as CoreEntity;

use Espo\Core\Di;


class Meeting extends Record implements
    Di\HookManagerAware
{
    use Di\HookManagerSetter;

    
    protected $duplicateIgnoreAttributeList = [
        'usersColumns',
        'contactsColumns',
        'leadsColumns',
    ];

    
    public function checkAssignment(Entity $entity): bool
    {
        $result = parent::checkAssignment($entity);

        if (!$result) {
            return false;
        }

        $userIdList = $entity->get('usersIds');

        if (!is_array($userIdList)) {
            $userIdList = [];
        }

        $newIdList = [];

        if (!$entity->isNew()) {
            $existingIdList = [];

            $usersCollection = $this->entityManager
                ->getRDBRepository($entity->getEntityType())
                ->getRelation($entity, 'users')
                ->select('id')
                ->find();

            foreach ($usersCollection as $user) {
                $existingIdList[] = $user->getId();
            }

            foreach ($userIdList as $id) {
                if (!in_array($id, $existingIdList)) {
                    $newIdList[] = $id;
                }
            }
        }
        else {
            $newIdList = $userIdList;
        }

        foreach ($newIdList as $userId) {
            if (!$this->acl->checkAssignmentPermission($userId)) {
                return false;
            }
        }

        return true;
    }
}
