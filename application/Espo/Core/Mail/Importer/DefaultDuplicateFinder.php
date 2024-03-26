<?php


namespace Espo\Core\Mail\Importer;

use Espo\Core\Mail\Message;
use Espo\Entities\Email;
use Espo\ORM\EntityManager;

class DefaultDuplicateFinder implements DuplicateFinder
{
    public function __construct(private EntityManager $entityManager)
    {}

    public function find(Email $email, Message $message): ?Email
    {
        if (!$email->getMessageId()) {
            return null;
        }

        return $this->entityManager
            ->getRDBRepositoryByClass(Email::class)
            ->select(['id', 'status'])
            ->where([
                'messageId' => $email->getMessageId(),
            ])
            ->findOne();
    }
}
