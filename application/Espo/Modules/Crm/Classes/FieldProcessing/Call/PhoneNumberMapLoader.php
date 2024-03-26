<?php


namespace Espo\Modules\Crm\Classes\FieldProcessing\Call;

use Espo\Modules\Crm\Entities\Call;
use Espo\ORM\Entity;
use Espo\Core\ORM\Entity as CoreEntity;
use Espo\Core\FieldProcessing\Loader;
use Espo\Core\FieldProcessing\Loader\Params;
use Espo\Core\ORM\EntityManager;

use stdClass;


class PhoneNumberMapLoader implements Loader
{
    private const ERASED_PART = 'ERASED:';

    public function __construct(private EntityManager $entityManager)
    {}

    public function process(Entity $entity, Params $params): void
    {
        $map = (object) [];

        assert($entity instanceof CoreEntity);

        $contactIdList = $entity->getLinkMultipleIdList('contacts');

        if (count($contactIdList)) {
            $this->populate($map, 'Contact', $contactIdList);
        }

        $leadIdList = $entity->getLinkMultipleIdList('leads');

        if (count($leadIdList)) {
            $this->populate($map, 'Lead', $leadIdList);
        }

        $entity->set('phoneNumbersMap', $map);
    }

    
    private function populate(stdClass $map, string $entityType, array $idList): void
    {
        $entityList = $this->entityManager
            ->getRDBRepository($entityType)
            ->where([
                'id' => $idList,
            ])
            ->select(['id', 'phoneNumber'])
            ->find();

        foreach ($entityList as $entity) {
            $phoneNumber = $entity->get('phoneNumber');

            if (!$phoneNumber) {
                continue;
            }

            if (strpos($phoneNumber, self::ERASED_PART) === 0) {
                continue;
            }

            $key = $entity->getEntityType() . '_' . $entity->getId();

            $map->$key = $phoneNumber;
        }
    }
}
