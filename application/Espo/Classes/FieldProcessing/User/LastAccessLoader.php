<?php


namespace Espo\Classes\FieldProcessing\User;

use Espo\Entities\AuthLogRecord;
use Espo\Entities\AuthToken;
use Espo\Entities\User;
use Espo\ORM\Entity;
use Espo\Core\Acl;
use Espo\Core\Acl\Table;
use Espo\Core\FieldProcessing\Loader;
use Espo\Core\FieldProcessing\Loader\Params;
use Espo\Core\ORM\EntityManager;

use DateTime;
use Exception;


class LastAccessLoader implements Loader
{
    private EntityManager $entityManager;
    private Acl $acl;

    public function __construct(EntityManager $entityManager, Acl $acl)
    {
        $this->entityManager = $entityManager;
        $this->acl = $acl;
    }

    public function process(Entity $entity, Params $params): void
    {
        $forbiddenFieldList = $this->acl
            ->getScopeForbiddenFieldList($entity->getEntityType(), Table::ACTION_READ);

        if (in_array('lastAccess', $forbiddenFieldList)) {
            return;
        }

        $authToken = $this->entityManager
            ->getRDBRepository(AuthToken::ENTITY_TYPE)
            ->select(['id', 'lastAccess'])
            ->where([
                'userId' => $entity->getId(),
            ])
            ->order('lastAccess', 'DESC')
            ->findOne();

        $lastAccess = null;

        if ($authToken) {
            $lastAccess = $authToken->get('lastAccess');
        }

        $dt = null;

        if ($lastAccess) {
            try {
                $dt = new DateTime($lastAccess);
            }
            catch (Exception) {}
        }

        $where = [
            'userId' => $entity->getId(),
            'isDenied' => false,
        ];

        if ($dt) {
            $where['requestTime>'] = $dt->format('U');
        }

        $authLogRecord = $this->entityManager
            ->getRDBRepository(AuthLogRecord::ENTITY_TYPE)
            ->select(['id', 'createdAt'])
            ->where($where)
            ->order('requestTime', true)
            ->findOne();

        if ($authLogRecord) {
            $lastAccess = $authLogRecord->get('createdAt');
        }

        $entity->set('lastAccess', $lastAccess);
    }
}
