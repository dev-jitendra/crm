<?php


namespace Espo\Entities;

use Espo\Core\Field\LinkParent;

use stdClass;

class Notification extends \Espo\Core\ORM\Entity
{
    public const ENTITY_TYPE = 'Notification';

    public const TYPE_ENTITY_REMOVED = 'EntityRemoved';
    public const TYPE_ASSIGN = 'Assign';
    public const TYPE_EMAIL_RECEIVED = 'EmailReceived';
    public const TYPE_NOTE = 'Note';
    public const TYPE_MENTION_IN_POST = 'MentionInPost';
    public const TYPE_MESSAGE = 'Message';
    public const TYPE_SYSTEM = 'System';

    public function getType(): ?string
    {
        return $this->get('type');
    }

    public function setMessage(?string $message): self
    {
        $this->set('message', $message);

        return $this;
    }

    public function setType(string $type): self
    {
        $this->set('type', $type);

        return $this;
    }

    public function getData(): ?stdClass
    {
        return $this->get('data');
    }

    public function setData(stdClass $data): self
    {
        $this->set('data', $data);

        return $this;
    }

    public function setUserId(string $userId): self
    {
        $this->set('userId', $userId);

        return $this;
    }

    public function getRelated(): ?LinkParent
    {
        
        return $this->getValueObject('related');
    }

    public function setRelated(?LinkParent $related): self
    {
        $this->setValueObject('related', $related);

        return $this;
    }

    public function setRelatedType(?string $relatedType): self
    {
        $this->set('relatedType', $relatedType);

        return $this;
    }

    public function setRelatedId(?string $relatedId): self
    {
        $this->set('relatedId', $relatedId);

        return $this;
    }
}
