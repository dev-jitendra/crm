<?php


namespace Espo\Core\Field\EmailAddress;

use Espo\Entities\EmailAddress as EmailAddressEntity;
use Espo\Repositories\EmailAddress as Repository;

use Espo\ORM\Entity;
use Espo\ORM\EntityManager;
use Espo\ORM\Value\ValueFactory;

use Espo\Core\Field\EmailAddress;
use Espo\Core\Field\EmailAddressGroup;
use Espo\Core\Utils\Metadata;

use RuntimeException;
use stdClass;


class EmailAddressGroupFactory implements ValueFactory
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

        if ($type !== 'email') {
            return false;
        }

        return true;
    }

    public function createFromEntity(Entity $entity, string $field): EmailAddressGroup
    {
        if (!$this->isCreatableFromEntity($entity, $field)) {
            throw new RuntimeException();
        }

        $emailAddressList = [];

        $primaryEmailAddress = null;

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
            
            $repository = $this->entityManager->getRepository(EmailAddressEntity::ENTITY_TYPE);

            $dataList = $repository->getEmailAddressData($entity);
        }

        foreach ($dataList as $item) {
            $emailAddress = EmailAddress::create($item->emailAddress);

            if ($item->optOut ?? false) {
                $emailAddress = $emailAddress->optedOut();
            }

            if ($item->invalid ?? false) {
                $emailAddress = $emailAddress->invalid();
            }

            if ($item->primary ?? false) {
                $primaryEmailAddress = $emailAddress;
            }

            $emailAddressList[] = $emailAddress;
        }

        $group = EmailAddressGroup::create($emailAddressList);

        if ($primaryEmailAddress) {
            $group = $group->withPrimary($primaryEmailAddress);
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
