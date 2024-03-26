<?php


namespace Espo\Repositories;

use Espo\ORM\Entity;
use Espo\Core\Repositories\Database;
use Espo\Repositories\UserData as UserDataRepository;
use Espo\Entities\UserData;
use Espo\Entities\User as UserEntity;


class User extends Database
{
    private const AUTHENTICATION_METHOD_HMAC = 'Hmac';

    
    protected function beforeSave(Entity $entity, array $options = [])
    {
        if ($entity->has('type') && !$entity->getType()) {
            $entity->set('type', UserEntity::TYPE_REGULAR);
        }

        if ($entity->isApi()) {
            if ($entity->isAttributeChanged('userName')) {
                $entity->set('lastName', $entity->getUserName());
            }

            if ($entity->has('authMethod') && $entity->getAuthMethod() !== self::AUTHENTICATION_METHOD_HMAC) {
                $entity->clear('secretKey');
            }
        } else {
            if ($entity->isAttributeChanged('type')) {
                $entity->set('authMethod', null);
            }
        }

        parent::beforeSave($entity, $options);

        if ($entity->has('type') && !$entity->isPortal()) {
            $entity->set('portalRolesIds', []);
            $entity->set('portalRolesNames', (object) []);
            $entity->set('portalsIds', []);
            $entity->set('portalsNames', (object) []);
        }

        if ($entity->has('type') && $entity->isPortal()) {
            $entity->set('rolesIds', []);
            $entity->set('rolesNames', (object) []);
            $entity->set('teamsIds', []);
            $entity->set('teamsNames', (object) []);
            $entity->set('defaultTeamId', null);
            $entity->set('defaultTeamName', null);
        }
    }

    
    protected function afterSave(Entity $entity, array $options = [])
    {
        if ($this->entityManager->getLocker()->isLocked()) {
            $this->entityManager->getLocker()->commit();
        }

        parent::afterSave($entity, $options);
    }

    
    protected function afterRemove(Entity $entity, array $options = [])
    {
        parent::afterRemove($entity, $options);

        $userData = $this->getUserDataRepository()->getByUserId($entity->getId());

        if ($userData) {
            $this->entityManager->removeEntity($userData);
        }
    }

    
    public function checkBelongsToAnyOfTeams(string $userId, array $teamIds): bool
    {
        if ($teamIds === []) {
            return false;
        }

        return (bool) $this->entityManager
            ->getRDBRepository('TeamUser')
            ->where([
                'deleted' => false,
                'userId' => $userId,
                'teamId' => $teamIds,
            ])
            ->findOne();
    }

    private function getUserDataRepository(): UserDataRepository
    {
        
        return $this->entityManager->getRepository(UserData::ENTITY_TYPE);
    }
}
