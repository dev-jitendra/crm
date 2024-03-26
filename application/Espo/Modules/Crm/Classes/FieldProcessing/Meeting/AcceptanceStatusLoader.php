<?php


namespace Espo\Modules\Crm\Classes\FieldProcessing\Meeting;

use Espo\Entities\User;
use Espo\ORM\Entity;
use Espo\ORM\EntityManager;

use Espo\Core\FieldProcessing\Loader;
use Espo\Core\FieldProcessing\Loader\Params;


class AcceptanceStatusLoader implements Loader
{
    private EntityManager $entityManager;
    private User $user;

    private const ATTR_ACCEPTANCE_STATUS = 'acceptanceStatus';

    public function __construct(EntityManager $entityManager, User $user)
    {
        $this->entityManager = $entityManager;
        $this->user = $user;
    }

    public function process(Entity $entity, Params $params): void
    {
        if (!$params->hasInSelect(self::ATTR_ACCEPTANCE_STATUS)) {
            return;
        }

        if ($entity->has(self::ATTR_ACCEPTANCE_STATUS)) {
            return;
        }

        $attribute = self::ATTR_ACCEPTANCE_STATUS;

        $user = $this->entityManager
            ->getRDBRepository($entity->getEntityType())
            ->getRelation($entity, 'users')
            ->where([
                'id' => $this->user->getId(),
            ])
            ->select([$attribute])
            ->findOne();

        $value = null;

        if ($user) {
            $value = $user->get($attribute);
        }

        $entity->set(self::ATTR_ACCEPTANCE_STATUS, $value);
    }
}
