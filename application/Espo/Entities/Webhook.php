<?php


namespace Espo\Entities;

class Webhook extends \Espo\Core\ORM\Entity
{
    public const ENTITY_TYPE = 'Webhook';

    public function getEvent(): ?string
    {
        return $this->get('event');
    }

    public function getSecretKey(): ?string
    {
        return $this->get('secretKey');
    }

    public function getUrl(): ?string
    {
        return $this->get('url');
    }
}
