<?php


namespace Espo\Core\Field\PhoneNumber;

use Espo\Entities\PhoneNumber as PhoneNumberEntity;
use Espo\Repositories\PhoneNumber as Repository;

use Espo\ORM\Entity;
use Espo\ORM\EntityManager;
use Espo\ORM\Value\ValueFactory;

use Espo\Core\Field\PhoneNumber;
use Espo\Core\Field\PhoneNumberGroup;
use Espo\Core\Utils\Metadata;

use RuntimeException;
use stdClass;


class PhoneNumberGroupFactory implements ValueFactory
{
    private Metadata $metadata;
    private EntityManager $entityManager;

    
    public function __construct(Metadata $metadata, EntityManager $entityManager)
    {
        $this->metadata = $metadata;
        $this->entityManager = $entityManager;
    }

    public function isCreatableFromEntity(Entity $entity, string $field): bool
    {
        $type = $this->metadata->get(['entityDefs', $entity->getEntityType(), 'fields', $field, 'type']);

        if ($type !== 'phone') {
            return false;
        }

        return true;
    }

    public function createFromEntity(Entity $entity, string $field): PhoneNumberGroup
    {
        if (!$this->isCreatableFromEntity($entity, $field)) {
            throw new RuntimeException();
        }

        $phoneNumberList = [];

        $primaryPhoneNumber = null;

        $dataList = null;

        $dataAttribute = $field . 'Data';

        if ($entity->has($dataAttribute)) {
            $dataList = $this->sanitizeDataList(
                $entity->get($dataAttribute)
            );
        }

        if (!$dataList && $entity->has($field) && !$entity->get($field)) {
            $dataList = [];
        }

        if (!$dataList) {
            
            $repository = $this->entityManager->getRepository(PhoneNumberEntity::ENTITY_TYPE);

            $dataList = $repository->getPhoneNumberData($entity);
        }

        foreach ($dataList as $item) {
            $phoneNumber = PhoneNumber::create($item->phoneNumber);

            if ($item->type ?? false) {
                $phoneNumber = $phoneNumber->withType($item->type);
            }

            if ($item->optOut ?? false) {
                $phoneNumber = $phoneNumber->optedOut();
            }

            if ($item->invalid ?? false) {
                $phoneNumber = $phoneNumber->invalid();
            }

            if ($item->primary ?? false) {
                $primaryPhoneNumber = $phoneNumber;
            }

            $phoneNumberList[] = $phoneNumber;
        }

        $group = PhoneNumberGroup::create($phoneNumberList);

        if ($primaryPhoneNumber) {
            $group = $group->withPrimary($primaryPhoneNumber);
        }

        return $group;
    }

    
    private function sanitizeDataList(array $dataList): array
    {
        $sanitizedDataList = [];

        foreach ($dataList as $item) {
            if (is_array($item)) {
                $sanitizedDataList[] = (object) $item;

                continue;
            }

            if (!is_object($item)) {
                throw new RuntimeException("Bad data.");
            }

            $sanitizedDataList[] = $item;
        }

        return $sanitizedDataList;
    }
}
