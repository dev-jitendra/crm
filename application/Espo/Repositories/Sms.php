<?php


namespace Espo\Repositories;

use Espo\Entities\Sms as SmsEntity;
use Espo\Entities\PhoneNumber;

use Espo\Core\Repositories\Database;


class Sms extends Database
{
    public function loadFromField(SmsEntity $entity): void
    {
        if ($entity->get('fromPhoneNumberName')) {
            $entity->set('from', $entity->get('fromPhoneNumberName'));

            return;
        }

        $numberId = $entity->get('fromPhoneNumberId');

        if ($numberId) {
            $phoneNumber = $this->entityManager
                ->getRepository(PhoneNumber::ENTITY_TYPE)
                ->getById($numberId);

            if ($phoneNumber) {
                $entity->set('from', $phoneNumber->get('name'));

                return;
            }
        }

        $entity->set('from', null);
    }

    public function loadToField(SmsEntity $entity): void
    {
        $entity->loadLinkMultipleField('toPhoneNumbers');

        $names = $entity->get('toPhoneNumbersNames');

        if (empty($names)) {
            $entity->set('to', null);

            return;
        }

        $list = [];

        foreach ($names as $address) {
            $list[] = $address;
        }

        $entity->set('to', implode(';', $list));
    }
}
