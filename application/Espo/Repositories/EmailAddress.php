<?php


namespace Espo\Repositories;

use Espo\Core\Repositories\Database;
use Espo\Entities\User as UserEntity;
use Espo\ORM\Entity;
use Espo\Entities\EmailAddress as EmailAddressEntity;
use Espo\Core\Di;

use stdClass;


class EmailAddress extends Database implements
    Di\ApplicationStateAware,
    Di\AclManagerAware,
    Di\ConfigAware
{
    use Di\ApplicationStateSetter;
    use Di\AclManagerSetter;
    use Di\ConfigSetter;

    protected $hooksDisabled = true;

    private const LOOKUP_SMALL_MAX_SIZE = 20;
    private const LOOKUP_MAX_SIZE = 50;

    
    public function getIdListFormAddressList(array $addressList = []): array
    {
        return $this->getIds($addressList);
    }

    
    public function getIds(array $addressList = []): array
    {
        if (empty($addressList)) {
            return [];
        }

        $ids = [];

        $lowerAddressList = [];

        foreach ($addressList as $address) {
            $lowerAddressList[] = trim(strtolower($address));
        }

        $eaCollection = $this
            ->where(['lower' => $lowerAddressList])
            ->find();

        $exist = [];

        foreach ($eaCollection as $ea) {
            $ids[] = $ea->getId();
            $exist[] = $ea->get('lower');
        }

        foreach ($addressList as $address) {
            $address = trim($address);

            if (empty($address) || !filter_var($address, FILTER_VALIDATE_EMAIL)) {
                continue;
            }

            if (!in_array(strtolower($address), $exist)) {
                $ea = $this->getNew();

                $ea->set('name', $address);

                $this->save($ea);

                $ids[] = $ea->getId();
            }
        }

        return $ids;
    }

    
    public function getEmailAddressData(Entity $entity): array
    {
        if (!$entity->hasId()) {
            return [];
        }

        $dataList = [];

        $emailAddressList = $this
            ->select(['name', 'lower', 'invalid', 'optOut', ['ee.primary', 'primary']])
            ->join(
                EmailAddressEntity::RELATION_ENTITY_EMAIL_ADDRESS,
                'ee',
                [
                    'ee.emailAddressId:' => 'id',
                ]
            )
            ->where([
                'ee.entityId' => $entity->getId(),
                'ee.entityType' => $entity->getEntityType(),
                'ee.deleted' => false,
            ])
            ->order('ee.primary', true)
            ->find();

        foreach ($emailAddressList as $emailAddress) {
            $item = (object) [
                'emailAddress' => $emailAddress->get('name'),
                'lower' => $emailAddress->get('lower'),
                'primary' => $emailAddress->get('primary'),
                'optOut' => $emailAddress->get('optOut'),
                'invalid' => $emailAddress->get('invalid'),
            ];

            $dataList[] = $item;
        }

        return $dataList;
    }

    public function getByAddress(string $address): ?EmailAddressEntity
    {
        
        return $this->where(['lower' => strtolower($address)])->findOne();
    }

    
    public function getEntityListByAddressId(
        string $emailAddressId,
        ?Entity $exceptionEntity = null,
        ?string $entityType = null,
        bool $onlyName = false
    ): array {

        $entityList = [];

        $where = [
            'emailAddressId' => $emailAddressId,
        ];

        if ($exceptionEntity) {
            $where[] = [
                'OR' => [
                    'entityType!=' => $exceptionEntity->getEntityType(),
                    'entityId!=' => $exceptionEntity->getId(),
                ]
            ];
        }

        if ($entityType) {
            $where[] = [
                'entityType' => $entityType,
            ];
        }

        $itemList = $this->entityManager
            ->getRDBRepository(EmailAddressEntity::RELATION_ENTITY_EMAIL_ADDRESS)
            ->sth()
            ->select(['entityType', 'entityId'])
            ->where($where)
            ->limit(0, self::LOOKUP_MAX_SIZE)
            ->find();

        foreach ($itemList as $item) {
            $itemEntityType = $item->get('entityType');
            $itemEntityId = $item->get('entityId');

            if (!$itemEntityType || !$itemEntityId) {
                continue;
            }

            if (!$this->entityManager->hasRepository($itemEntityType)) {
                continue;
            }

            if ($onlyName) {
                $select = ['id', 'name'];

                if ($itemEntityType === UserEntity::ENTITY_TYPE) {
                    $select[] = 'isActive';
                }

                $entity = $this->entityManager
                    ->getRDBRepository($itemEntityType)
                    ->select($select)
                    ->where(['id' => $itemEntityId])
                    ->findOne();
            }
            else {
                $entity = $this->entityManager->getEntity($itemEntityType, $itemEntityId);
            }

            if (!$entity) {
                continue;
            }

            if ($entity instanceof UserEntity && !$entity->isActive()) {
                continue;
            }

            $entityList[] = $entity;
        }

        return $entityList;
    }

    public function getEntityByAddressId(
        string $emailAddressId,
        ?string $entityType = null,
        bool $onlyName = false
    ): ?Entity {

        $where = [
            'emailAddressId' => $emailAddressId,
        ];

        if ($entityType) {
            $where[] = ['entityType' => $entityType];
        }

        $itemList = $this->entityManager
            ->getRDBRepository(EmailAddressEntity::RELATION_ENTITY_EMAIL_ADDRESS)
            ->sth()
            ->select(['entityType', 'entityId'])
            ->where($where)
            ->limit(0, self::LOOKUP_SMALL_MAX_SIZE)
            ->order([
                ['primary', 'DESC'],
                ['LIST:entityType:User,Contact,Lead,Account'],
            ])
            ->find();

        foreach ($itemList as $item) {
            $itemEntityType = $item->get('entityType');
            $itemEntityId = $item->get('entityId');

            if (!$itemEntityType || !$itemEntityId) {
                continue;
            }

            if (!$this->entityManager->hasRepository($itemEntityType)) {
                continue;
            }

            if ($onlyName) {
                $select = ['id', 'name'];

                if ($itemEntityType === UserEntity::ENTITY_TYPE) {
                    $select[] = 'isActive';
                }

                $entity = $this->entityManager
                    ->getRDBRepository($itemEntityType)
                    ->select($select)
                    ->where(['id' => $itemEntityId])
                    ->findOne();
            }
            else {
                $entity = $this->entityManager->getEntity($itemEntityType, $itemEntityId);
            }

            if ($entity) {
                if ($entity instanceof UserEntity) {
                    if (!$entity->isActive()) {
                        continue;
                    }
                }

                return $entity;
            }
        }

        return null;
    }

    
    public function getEntityByAddress(string $address, ?string $entityType = null, ?array $order = null): ?Entity
    {
        $order ??= $this->config->get('emailAddressEntityLookupDefaultOrder') ?? [];

        $selectBuilder = $this->entityManager
            ->getRDBRepository(EmailAddressEntity::RELATION_ENTITY_EMAIL_ADDRESS)
            ->select();

        $selectBuilder
            ->select(['entityType', 'entityId'])
            ->sth()
            ->join(
                EmailAddressEntity::ENTITY_TYPE,
                'ea',
                ['ea.id:' => 'emailAddressId', 'ea.deleted' => false]
            )
            ->where('ea.lower=', strtolower($address))
            ->order([
                ['LIST:entityType:' . implode(',', $order)],
                ['primary', 'DESC'],
            ])
            ->limit(0, self::LOOKUP_MAX_SIZE);

        if ($entityType) {
            $selectBuilder->where('entityType=', $entityType);
        }

        foreach ($selectBuilder->find() as $item) {
            $itemEntityType = $item->get('entityType');
            $itemEntityId = $item->get('entityId');

            if (!$itemEntityType || !$itemEntityId) {
                continue;
            }

            if (!$this->entityManager->hasRepository($itemEntityType)) {
                continue;
            }

            $entity = $this->entityManager->getEntity($itemEntityType, $itemEntityId);

            if ($entity) {
                if ($entity instanceof UserEntity) {
                    if (!$entity->isActive()) {
                        continue;
                    }
                }

                return $entity;
            }
        }

        return null;
    }

    public function markAddressOptedOut(string $address, bool $isOptedOut = true): void
    {
        $emailAddress = $this->getByAddress($address);

        if (!$emailAddress) {
            return;
        }

        $emailAddress->set('optOut', $isOptedOut);

        $this->save($emailAddress);
    }

    public function markAddressInvalid(string $address, bool $isInvalid = true): void
    {
        $emailAddress = $this->getByAddress($address);

        if (!$emailAddress) {
            return;
        }

        $emailAddress->set('invalid', $isInvalid);

        $this->save($emailAddress);
    }
}
