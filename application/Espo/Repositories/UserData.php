<?php


namespace Espo\Repositories;

use Espo\Core\ORM\Repository\Option\SaveOption;
use Espo\Entities\User as UserEntity;
use Espo\Entities\UserData as UserDataEntity;
use Espo\Core\Repositories\Database;


class UserData extends Database
{
    public function getByUserId(string $userId): ?UserDataEntity
    {
        
        $userData = $this
            ->where(['userId' => $userId])
            ->findOne();

        if ($userData) {
            return $userData;
        }

        $user = $this->entityManager
            ->getRepository(UserEntity::ENTITY_TYPE)
            ->getById($userId);

        if (!$user) {
            return null;
        }

        $userData = $this->getNew();

        $userData->set('userId', $userId);

        $this->save($userData, [
            SaveOption::SILENT => true,
            SaveOption::SKIP_HOOKS => true,
        ]);

        return $userData;
    }
}
