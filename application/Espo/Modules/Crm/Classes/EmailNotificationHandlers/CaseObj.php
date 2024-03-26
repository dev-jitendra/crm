<?php


namespace Espo\Modules\Crm\Classes\EmailNotificationHandlers;

use Espo\Core\Notification\EmailNotificationHandler;

use Espo\Core\Mail\SenderParams;
use Espo\Entities\InboundEmail;
use Espo\ORM\Entity;
use Espo\Entities\User;
use Espo\Entities\Email;

use Espo\ORM\EntityManager;

class CaseObj implements EmailNotificationHandler
{
    
    private $inboundEmailEntityHash = [];

    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function prepareEmail(Email $email, Entity $entity, User $user): void {}

    public function getSenderParams(Entity $entity, User $user): ?SenderParams
    {
        
        $inboundEmailId = $entity->get('inboundEmailId');

        if (!$inboundEmailId) {
            return null;
        }

        if (!array_key_exists($inboundEmailId, $this->inboundEmailEntityHash)) {
            $this->inboundEmailEntityHash[$inboundEmailId] =
                $this->entityManager->getEntityById(InboundEmail::ENTITY_TYPE, $inboundEmailId);
        }

        $inboundEmail = $this->inboundEmailEntityHash[$inboundEmailId];

        if (!$inboundEmail) {
            return null;
        }

        $emailAddress = $inboundEmail->get('emailAddress');

        if (!$emailAddress) {
            return null;
        }

        return SenderParams::create()->withReplyToAddress($emailAddress);
    }
}
