<?php


namespace Espo\Modules\Crm\Classes\FieldProcessing\Meeting;

use Espo\Entities\Email;
use Espo\Modules\Crm\Entities\Meeting;
use Espo\ORM\Entity;
use Espo\ORM\EntityManager;
use Espo\Core\FieldProcessing\Saver;
use Espo\Core\FieldProcessing\Saver\Params;
use Espo\Core\Mail\Event\EventFactory;

use ICal\ICal;


class SourceEmailSaver implements Saver
{
    public function __construct(private EntityManager $entityManager)
    {}

    
    public function process(Entity $entity, Params $params): void
    {
        if (!$entity->isNew()) {
            return;
        }

        $emailId = $entity->get('sourceEmailId');

        if (!$emailId) {
            return;
        }

        $email = $this->entityManager->getEntityById(Email::ENTITY_TYPE, $emailId);

        if (!$email) {
            return;
        }

        $icsContents = $email->get('icsContents');

        if ($icsContents === null) {
            return;
        }

        $ical = new ICal();

        $ical->initString($icsContents);

        $espoEvent = EventFactory::createFromU01jmg3Ical($ical);

        $email->set('createdEventId', $entity->getId());
        $email->set('createdEventType', $entity->getEntityType());
        $email->set('icsEventUid', $espoEvent->getUid());

        $this->entityManager->saveEntity($email);
    }
}
