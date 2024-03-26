<?php


namespace Espo\Classes\FieldProcessing\Email;

use Espo\Modules\Crm\Entities\Call;
use Espo\Modules\Crm\Entities\Meeting;
use Espo\ORM\Entity;
use Espo\ORM\EntityManager;
use Espo\Repositories\EmailAddress as EmailAddressRepository;
use Espo\Entities\EmailAddress;
use Espo\Entities\Email;
use Espo\Core\FieldProcessing\Loader;
use Espo\Core\FieldProcessing\Loader\Params;
use Espo\Core\Mail\Event\Event as EspoEvent;
use Espo\Core\Mail\Event\EventFactory;
use Espo\Core\Utils\Log;

use ICal\Event;
use ICal\ICal;

use Throwable;
use stdClass;


class IcsDataLoader implements Loader
{
    
    private $entityTypeLinkMap = [
        'User' => 'users',
        'Contact' => 'contacts',
        'Lead' => 'leads',
    ];

    public function __construct(private EntityManager $entityManager, private Log $log)
    {}

    public function process(Entity $entity, Params $params): void
    {
        $icsContents = $entity->get('icsContents');

        if ($icsContents === null) {
            return;
        }

        $ical = new ICal();

        $ical->initString($icsContents);

        
        $event = $ical->events()[0] ?? null;

        if ($event === null) {
            return;
        }

        if ($event->status === 'CANCELLED') {
            return;
        }

        $espoEvent = EventFactory::createFromU01jmg3Ical($ical);

        $valueMap = (object) [
            'sourceEmailId' => $entity->getId(),
        ];

        try {
            $valueMap->name = $espoEvent->getName();
            $valueMap->description = $espoEvent->getDescription();
            $valueMap->dateStart = $espoEvent->getDateStart();
            $valueMap->dateEnd = $espoEvent->getDateEnd();
            $valueMap->location = $espoEvent->getLocation();
            $valueMap->isAllDay = $espoEvent->isAllDay();

            if ($espoEvent->isAllDay()) {
                $valueMap->dateStartDate = $espoEvent->getDateStart();
                $valueMap->dateEndDate = $espoEvent->getDateEnd();
            }
        }
        catch (Throwable $e) {
            $this->log->warning("Error while converting ICS event '" . $entity->getId() . "': " . $e->getMessage());

            return;
        }

        if ($this->eventAlreadyExists($espoEvent)) {
            return;
        }

        
        $emailAddressRepository = $this->entityManager->getRepository(EmailAddress::ENTITY_TYPE);

        $attendeeEmailAddressList = $espoEvent->getAttendeeEmailAddressList();
        $organizerEmailAddress = $espoEvent->getOrganizerEmailAddress();

        if ($organizerEmailAddress) {
            $attendeeEmailAddressList[] = $organizerEmailAddress;
        }

        foreach ($attendeeEmailAddressList as $address) {
            $personEntity = $emailAddressRepository->getEntityByAddress($address);

            if (!$personEntity) {
                continue;
            }

            $link = $this->entityTypeLinkMap[$personEntity->getEntityType()] ?? null;

            if (!$link) {
                continue;
            }

            $idsAttribute = $link . 'Ids';
            $namesAttribute = $link . 'Names';

            $idList = $valueMap->$idsAttribute ?? [];
            $nameMap = $valueMap->$namesAttribute ?? (object) [];

            $idList[] = $personEntity->getId();
            $nameMap->{$personEntity->getId()} = $personEntity->get('name');

            $valueMap->$idsAttribute = $idList;
            $valueMap->$namesAttribute = $nameMap;
        }

        $eventData = (object) [
            'valueMap' => $valueMap,
            'uid' => $espoEvent->getUid(),
            'createdEvent' => null,
        ];

        $this->loadCreatedEvent($entity, $espoEvent, $eventData);

        $entity->set('icsEventData', $eventData);
        $entity->set('icsEventDateStart', $espoEvent->getDateStart());

        if ($espoEvent->isAllDay()) {
            $entity->set('icsEventDateStartDate', $espoEvent->getDateStart());
        }
    }

    private function loadCreatedEvent(Entity $entity, EspoEvent $espoEvent, stdClass $eventData): void
    {
        $emailSameEvent = $this->entityManager
            ->getRDBRepository(Email::ENTITY_TYPE)
            ->where([
                'icsEventUid' => $espoEvent->getUid(),
                'id!=' => $entity->getId()
            ])
            ->findOne();

        if (!$emailSameEvent) {
            return;
        }

        if (
            !$emailSameEvent->get('createdEventId') ||
            !$emailSameEvent->get('createdEventType')
        ) {
            return;
        }

        $createdEvent = $this->entityManager
            ->getEntity($emailSameEvent->get('createdEventType'), $emailSameEvent->get('createdEventId'));

        if (!$createdEvent) {
            return;
        }

        $eventData->createdEvent = (object) [
            'id' => $createdEvent->getId(),
            'entityType' => $emailSameEvent->getEntityType(),
            'name' => $createdEvent->get('name'),
        ];
    }

    private function eventAlreadyExists(EspoEvent $espoEvent): bool
    {
        $id = $espoEvent->getUid();

        if (!$id) {
            return false;
        }

        $found1 = $this->entityManager
            ->getRDBRepository(Meeting::ENTITY_TYPE)
            ->select(['id'])
            ->where(['id' => $id])
            ->findOne();

        if ($found1) {
            return true;
        }

        $found2 = $this->entityManager
            ->getRDBRepository(Call::ENTITY_TYPE)
            ->select(['id'])
            ->where(['id' => $id])
            ->findOne();

        if ($found2) {
            return true;
        }

        return false;
    }
}
