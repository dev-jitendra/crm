<?php


namespace Espo\Core\Sms;

use Espo\ORM\EntityManager;
use Espo\Entities\Sms as SmsEntity;


class SmsFactory
{
    public function __construct(private EntityManager $entityManager)
    {}

    
    public function create(): SmsEntity
    {
        
        $sms = $this->entityManager->getNewEntity(SmsEntity::ENTITY_TYPE);

        return $sms;
    }
}
