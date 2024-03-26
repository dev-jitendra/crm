<?php


namespace Espo\Repositories;

use Espo\Entities\User as UserEntity;
use Espo\ORM\Entity;
use Espo\Entities\PhoneNumber as PhoneNumberEntity;
use Espo\Core\Repositories\Database;
use Espo\Core\Di;

use stdClass;


class PhoneNumber extends Database implements

    Di\ApplicationStateAware,
    Di\AclManagerAware,
    Di\ConfigAware
{
    use Di\ApplicationStateSetter;
    use Di\AclManagerSetter;
    use Di\ConfigSetter;

    protected $hooksDisabled = true;

    private const ERASED_PREFIX = 'ERASED:';

    private const LOOKUP_SMALL_MAX_SIZE = 20;
    private const LOOKUP_MAX_SIZE = 50;

    
    public function getIds($numberList = []): array
    {
        if (empty($numberList)) {
            return [];
        }

        $ids = [];

        $phoneNumbers = $this
            ->where([
                'name' => $numberList,
                'hash' => null,
            ])
            ->find();

        $exist = [];

        foreach ($phoneNumbers as $phoneNumber) {
            $ids[] = $phoneNumber->getId();
            $exist[] = $phoneNumber->get('name');
        }

        foreach ($numberList as $number) {
            $number = trim($number);

            if (empty($number)) {
                continue;
            }

            if (!in_array($number, $exist)) {
                $phoneNumber = $this->getNew();
                $phoneNumber->set('name', $number);
                $this->save($phoneNumber);

                $ids[] = $phoneNumber->getId();
            }
        }

        return $ids;
    }

    
    public function getPhoneNumberData(Entity $entity): array
    {
        if (!$entity->hasId()) {
            return [];
        }

        $dataList = [];

        $numberList = $this
            ->select(['name', 'type', 'invalid', 'optOut', ['en.primary', 'primary']])
            ->join(
                PhoneNumberEntity::RELATION_ENTITY_PHONE_NUMBER,
                'en',
                [
                    'en.phoneNumberId:' => 'id',
                ]
            )
            ->where([
                'en.entityId' => $entity->getId(),
                'en.entityType' => $entity->getEntityType(),
                'en.deleted' => false,
            ])
            ->order('en.primary', true)
            ->find();

        foreach ($numberList as $number) {
            $item = (object) [
                'phoneNumber' => $number->get('name'),
                'type' => $number->get('type'),
                'primary' => $number->get('primary'),
                'optOut' => $number->get('optOut'),
                'invalid' => $number->get('invalid'),
            ];

            $dataList[] = $item;
        }

        return $dataList;
    }

    public function getByNumber(string $number): ?PhoneNumberEntity
    {
        
        return $this->where(['name' => $number])->findOne();
    }

    
    public function getEntityListByPhoneNumberId(string $phoneNumberId, ?Entity $exceptionEntity = null): array
    {
        $entityList = [];

        $where = [
            'phoneNumberId' => $phoneNumberId,
        ];

        if ($exceptionEntity) {
            $where[] = [
                'OR' => [
                    'entityType!=' => $exceptionEntity->getEntityType(),
                    'entityId!=' => $exceptionEntity->getId(),
                ]
            ];
        }

        $itemList = $this->entityManager
            ->getRDBRepository(PhoneNumberEntity::RELATION_ENTITY_PHONE_NUMBER)
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

            $entity = $this->entityManager->getEntity($itemEntityType, $itemEntityId);

            if (!$entity) {
                continue;
            }

            $entityList[] = $entity;
        }

        return $entityList;
    }

    
    public function getEntityByPhoneNumberId(
        string $phoneNumberId,
        ?string $entityType = null,
        ?array $order = null
    ): ?Entity {

        $order ??= $this->config->get('phoneNumberEntityLookupDefaultOrder') ?? [];

        $where = ['phoneNumberId' => $phoneNumberId];

        if ($entityType) {
            $where[] = ['entityType' => $entityType];
        }

        $collection = $this->entityManager
            ->getRDBRepository(PhoneNumberEntity::RELATION_ENTITY_PHONE_NUMBER)
            ->sth()
            ->select(['entityType', 'entityId'])
            ->where($where)
            ->limit(0, self::LOOKUP_SMALL_MAX_SIZE)
            ->order([
                ['LIST:entityType:' . implode(',', $order)],
                ['primary', 'DESC'],
            ])
            ->find();

        foreach ($collection as $item) {
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

    protected function beforeSave(Entity $entity, array $options = [])
    {
        parent::beforeSave($entity, $options);

        if ($entity->has('name')) {
            $number = $entity->get('name');

            if (is_string($number) && !str_starts_with($number, self::ERASED_PREFIX)) {
                $numeric = preg_replace('/[^0-9]/', '', $number);
            }
            else {
                $numeric = null;
            }

            $entity->set('numeric', $numeric);
        }
    }

    public function markNumberOptedOut(string $number, bool $isOptedOut = true): void
    {
        $phoneNumber = $this->getByNumber($number);

        if (!$phoneNumber) {
            return;
        }

        $phoneNumber->set('optOut', $isOptedOut);

        $this->save($phoneNumber);
    }

    public function markNumberInvalid(string $number, bool $isInvalid = true): void
    {
        $phoneNumber = $this->getByNumber($number);

        if (!$phoneNumber) {
            return;
        }

        $phoneNumber->set('invalid', $isInvalid);

        $this->save($phoneNumber);
    }
}
