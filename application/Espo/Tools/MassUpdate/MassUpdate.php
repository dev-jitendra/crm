<?php


namespace Espo\Tools\MassUpdate;

use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Exceptions\NotFound;
use Espo\Core\MassAction\Params;
use Espo\Core\MassAction\Result;
use Espo\Core\MassAction\MassActionFactory;

use Espo\Core\Utils\SystemUser;
use Espo\ORM\EntityManager;
use Espo\Entities\User;

use RuntimeException;


class MassUpdate
{
    private const ACTION = 'massUpdate';

    public function __construct(
        private MassActionFactory $massActionFactory,
        private EntityManager $entityManager
    ) {}

    
    public function process(Params $params, Data $data, ?User $user = null): Result
    {
        $entityType = $params->getEntityType();

        if (!$user) {
            $user = $this->entityManager
                ->getRDBRepositoryByClass(User::class)
                ->where(['userName' => SystemUser::NAME])
                ->findOne();
        }

        if (!$user) {
            throw new RuntimeException("No user.");
        }

        $action = $this->massActionFactory->createForUser(self::ACTION, $entityType, $user);

        return $action->process($params, $data->toMassActionData());
    }
}
