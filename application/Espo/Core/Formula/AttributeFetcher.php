<?php


namespace Espo\Core\Formula;

use Espo\Entities\EmailAddress;
use Espo\Entities\PhoneNumber;
use Espo\ORM\Entity;
use Espo\Core\ORM\Entity as CoreEntity;
use Espo\ORM\EntityManager;
use Espo\Repositories\EmailAddress as EmailAddressRepository;
use Espo\Repositories\PhoneNumber as PhoneNumberRepository;


class AttributeFetcher
{
    
    private $relatedEntitiesCacheMap = [];

    public function __construct(private EntityManager $entityManager) {}

    public function fetch(Entity $entity, string $attribute, bool $getFetchedAttribute = false): mixed
    {
        if (str_contains($attribute, '.')) {
            $arr = explode('.', $attribute);

            $relationName = $arr[0];

            $key = $this->buildKey($entity, $relationName);

            if (
                !array_key_exists($key, $this->relatedEntitiesCacheMap) &&
                $entity->hasRelation($relationName) &&
                !in_array(
                    $entity->getRelationType($relationName),
                    [Entity::MANY_MANY, Entity::HAS_MANY, Entity::HAS_CHILDREN]
                )
            ) {
                $this->relatedEntitiesCacheMap[$key] = $this->entityManager
                    ->getRDBRepository($entity->getEntityType())
                    ->getRelation($entity, $relationName)
                    ->findOne();
            }

            $relatedEntity = $this->relatedEntitiesCacheMap[$key] ?? null;

            if (
                $relatedEntity instanceof Entity &&
                count($arr) > 1
            ) {
                return $this->fetch($relatedEntity, $arr[1]);
            }

            return null;
        }

        if ($getFetchedAttribute) {
            return $entity->getFetched($attribute);
        }

        if (
            $entity instanceof CoreEntity &&
            !$entity->has($attribute)
        ) {
            $this->load($entity, $attribute);
        }

        return $entity->get($attribute);
    }

    private function load(CoreEntity $entity, string $attribute): void
    {
        if ($entity->getAttributeParam($attribute, 'isParentName')) {
            
            $relationName = $entity->getAttributeParam($attribute, 'relation');

            if ($relationName) {
                $entity->loadParentNameField($relationName);
            }

            return;
        }

        if ($entity->getAttributeParam($attribute, 'isLinkMultipleIdList')) {
            
            $relationName = $entity->getAttributeParam($attribute, 'relation');

            if ($relationName) {
                $entity->loadLinkMultipleField($relationName);
            }

            return;
        }

        if ($entity->getAttributeParam($attribute, 'isEmailAddressData')) {
            
            $fieldName = $entity->getAttributeParam($attribute, 'field');

            if (!$fieldName) {
                return;
            }

            
            $emailAddressRepository = $this->entityManager->getRepository(EmailAddress::ENTITY_TYPE);

            $data = $emailAddressRepository->getEmailAddressData($entity);

            $entity->set($attribute, $data);
            $entity->setFetched($attribute, $data);;

            return;
        }

        if ($entity->getAttributeParam($attribute, 'isPhoneNumberData')) {
            
            $fieldName = $entity->getAttributeParam($attribute, 'field');

            if (!$fieldName) {
                return;
            }

            
            $phoneNumberRepository = $this->entityManager->getRepository(PhoneNumber::ENTITY_TYPE);

            $data = $phoneNumberRepository->getPhoneNumberData($entity);

            $entity->set($attribute, $data);
            $entity->setFetched($attribute, $data);;

            return;
        }
    }

    public function resetRuntimeCache(): void
    {
        $this->relatedEntitiesCacheMap = [];
    }

    private function buildKey(Entity $entity, string $link): string
    {
        return spl_object_hash($entity) . '-' . $link;
    }
}
