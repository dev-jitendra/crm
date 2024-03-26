<?php


namespace Espo\Entities;

use Espo\Core\Field\LinkMultiple;
use Espo\Core\ORM\Entity;
use Espo\Core\Sms\Sms as SmsInterface;
use Espo\Core\Field\DateTime;

use Espo\Repositories\Sms as SmsRepository;

use RuntimeException;

class Sms extends Entity implements SmsInterface
{
    public const ENTITY_TYPE = 'Sms';

    public const STATUS_ARCHIVED = 'Archived';
    public const STATUS_SENT = 'Sent';
    public const STATUS_SENDING = 'Sending';
    public const STATUS_DRAFT = 'Draft';
    public const STATUS_FAILED = 'Failed';

    public function getDateSent(): ?DateTime
    {
        
        return $this->getValueObject('dateTime');
    }

    public function getCreatedAt(): ?DateTime
    {
        
        return $this->getValueObject('createdAt');
    }

    public function getBody(): string
    {
        return $this->get('body') ?? '';
    }

    public function getStatus(): ?string
    {
        return $this->get('status');
    }

    public function setBody(?string $body): self
    {
        $this->set('body', $body);

        return $this;
    }

    public function setFromNumber(?string $number): self
    {
        $this->set('from', $number);

        return $this;
    }

    public function addToNumber(string $number): self
    {
        $list = $this->getToNumberList();

        $list[] = $number;

        $this->set('to', implode(';', $list));

        return $this;
    }

    public function getFromNumber(): ?string
    {
        if (!$this->hasInContainer('from') && !$this->isNew()) {
            $this->getSmsRepository()->loadFromField($this);
        }

        return $this->get('from');
    }

    public function getFromName(): ?string
    {
        return $this->get('fromName');
    }

    
    public function getToNumberList(): array
    {
        if (!$this->hasInContainer('to') && !$this->isNew()) {
            $this->getSmsRepository()->loadToField($this);
        }

        $value = $this->get('to');

        if (!$value) {
            return [];
        }

        return explode(';', $value);
    }

    public function setAsSent(): self
    {
        $this->set('status', Sms::STATUS_SENT);

        if (!$this->get('dateSent')) {
            $this->set('dateSent', DateTime::createNow()->toString());
        }

        return $this;
    }

    public function setStatus(string $status): self
    {
        $this->set('status', $status);

        return $this;
    }

    private function getSmsRepository(): SmsRepository
    {
        if (!$this->entityManager) {
            throw new RuntimeException();
        }

        
        return $this->entityManager->getRepository(Sms::ENTITY_TYPE);
    }

    public function getTeams(): LinkMultiple
    {
        
        return $this->getValueObject('teams');
    }
}
