<?php


namespace Espo\Classes\FieldProcessing\Email;

use Espo\Entities\Email;
use Espo\ORM\Entity;
use Espo\Core\FieldProcessing\Loader;
use Espo\Core\FieldProcessing\Loader\Params;
use Espo\Core\ORM\EntityManager;
use Espo\Entities\User;


class UserColumnsLoader implements Loader
{
    private EntityManager $entityManager;
    private User $user;

    public function __construct(EntityManager $entityManager, User $user)
    {
        $this->entityManager = $entityManager;
        $this->user = $user;
    }

    public function process(Entity $entity, Params $params): void
    {
        $emailUser = $this->entityManager
            ->getRDBRepository(Email::RELATIONSHIP_EMAIL_USER)
            ->select([
                Email::USERS_COLUMN_IS_READ,
                Email::USERS_COLUMN_IS_IMPORTANT,
                Email::USERS_COLUMN_IN_TRASH,
            ])
            ->where([
                'deleted' => false,
                'userId' => $this->user->getId(),
                'emailId' => $entity->getId(),
            ])
            ->findOne();

        if (!$emailUser) {
            $entity->set(Email::USERS_COLUMN_IS_READ, null);
            $entity->clear(Email::USERS_COLUMN_IS_IMPORTANT);
            $entity->clear(Email::USERS_COLUMN_IN_TRASH);

            return;
        }

        $entity->set([
            Email::USERS_COLUMN_IS_READ => $emailUser->get(Email::USERS_COLUMN_IS_READ),
            Email::USERS_COLUMN_IS_IMPORTANT => $emailUser->get(Email::USERS_COLUMN_IS_IMPORTANT),
            Email::USERS_COLUMN_IN_TRASH => $emailUser->get(Email::USERS_COLUMN_IN_TRASH),
        ]);
    }
}
