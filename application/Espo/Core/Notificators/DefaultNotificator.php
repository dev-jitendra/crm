<?php


namespace Espo\Core\Notificators;

use Espo\ORM\Entity;
use Espo\Entities\User;
use Espo\Core\Notification\AssignmentNotificator\Params;
use Espo\Core\Notification\DefaultAssignmentNotificator;
use Espo\Core\ORM\EntityManager;


class DefaultNotificator
{
    protected $entityType; 
    protected $user; 
    protected $entityManager; 
    private $base; 

    public function __construct(User $user, EntityManager $entityManager, DefaultAssignmentNotificator $base)
    {
        $this->user = $user;
        $this->entityManager = $entityManager;
        $this->base = $base;
    }

    public function process(Entity $entity, array $options = []) 
    {
        $this->base->process($entity, Params::create()->withRawOptions($options));
    }

    
    protected function getEntityManager() 
    {
        return $this->entityManager;
    }

    
    protected function getUser() 
    {
        return $this->user;
    }
}
