<?php


namespace Espo\Tools\Email;

use Espo\Core\Field\EmailAddress;
use Espo\ORM\Entity;
use stdClass;

class EmailAddressEntityPair
{
    private EmailAddress $emailAddress;
    private Entity $entity;

    public function __construct(
        EmailAddress $emailAddress,
        Entity $entity
    ) {
        $this->emailAddress = $emailAddress;
        $this->entity = $entity;
    }

    public function getEmailAddress(): EmailAddress
    {
        return $this->emailAddress;
    }

    public function getEntity(): Entity
    {
        return $this->entity;
    }

    public function getValueMap(): stdClass
    {
        return (object) [
            'emailAddress' => $this->emailAddress->getAddress(),
            'name' => $this->entity->get('name'),
            'entityId' => $this->entity->getId(),
            'entityType' => $this->entity->getEntityType(),
        ];
    }
}
