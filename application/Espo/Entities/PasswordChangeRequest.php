<?php


namespace Espo\Entities;

use Espo\Core\ORM\Entity;
use UnexpectedValueException;

class PasswordChangeRequest extends Entity
{
    public const ENTITY_TYPE = 'PasswordChangeRequest';

    public function getUrl(): ?string
    {
        return $this->get('url');
    }

    public function getRequestId(): string
    {
        return $this->get('requestId');
    }

    public function getUserId(): string
    {
        $userId = $this->get('userId');

        if ($userId === null) {
            throw new UnexpectedValueException();
        }

        return $userId;
    }
}
