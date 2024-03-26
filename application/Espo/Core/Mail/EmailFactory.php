<?php


namespace Espo\Core\Mail;

use Espo\ORM\EntityManager;
use Espo\Entities\Email;


class EmailFactory
{
    public function __construct(private EntityManager $entityManager)
    {}

    
    public function create(): Email
    {
        
        $email = $this->entityManager->getNewEntity(Email::ENTITY_TYPE);

        return $email;
    }
}
