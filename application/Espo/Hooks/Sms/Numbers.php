<?php


namespace Espo\Hooks\Sms;

use Espo\ORM\EntityManager;
use Espo\Entities\Sms;
use Espo\Entities\PhoneNumber;
use Espo\Repositories\PhoneNumber as PhoneNumberRepository;

use Espo\ORM\Entity;

class Numbers
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function beforeSave(Entity $entity): void
    {
        assert($entity instanceof Sms);

        $this->processNumbers($entity);
    }

    private function processNumbers(Sms $entity): void
    {
        if ($entity->has('from')) {
            $this->processFrom($entity);
        }

        if ($entity->has('to')) {
            $this->processTo($entity);
        }
    }

    private function processFrom(Sms $entity): void
    {
        $from = $entity->get('from');

        $entity->set('fromPhoneNumberId', null);
        $entity->set('fromEmailAddressName', null);

        if (!$from) {
            return;
        }

        $numberIds = $this->getPhoneNumberRepository()->getIds([$from]);

        if (!count($numberIds)) {
            return;
        }

        $entity->set('fromEmailAddressId', $numberIds[0]);
        $entity->set('fromEmailAddressName', $from);
    }

    private function processTo(Sms $entity): void
    {
        $entity->setLinkMultipleIdList('toPhoneNumbers', []);

        $to = $entity->get('to');

        if ($to === null || !$to) {
            return;
        }

        $numberList = array_map(
            function (string $item): string {
                return trim($item);
            },
            explode(';', $to)
        );

        $numberIds = $this->getPhoneNumberRepository()->getIds($numberList);

        $entity->setLinkMultipleIdList('toPhoneNumbers', $numberIds);
    }

    private function getPhoneNumberRepository(): PhoneNumberRepository
    {
        
        return $this->entityManager->getRepository(PhoneNumber::ENTITY_TYPE);
    }
}
