<?php


namespace Espo\Core\Portal;

use Espo\ORM\Entity;

use Espo\Entities\User;

use Espo\Core\Acl as BaseAcl;

class Acl extends BaseAcl
{
    public function __construct(AclManager $aclManager, User $user)
    {
        parent::__construct($aclManager, $user);
    }

    
    public function checkReadOnlyAccount(string $scope): bool
    {
        
        $aclManager = $this->aclManager;

        return $aclManager->checkReadOnlyAccount($this->user, $scope);
    }

    
    public function checkReadOnlyContact(string $scope): bool
    {
        
        $aclManager = $this->aclManager;

        return $aclManager->checkReadOnlyContact($this->user, $scope);
    }

    
    public function checkOwnershipAccount(Entity $entity): bool
    {
        
        $aclManager = $this->aclManager;

        return $aclManager->checkOwnershipAccount($this->user, $entity);
    }

    
    public function checkOwnershipContact(Entity $entity): bool
    {
        
        $aclManager = $this->aclManager;

        return $aclManager->checkOwnershipContact($this->user, $entity);
    }

    
    public function checkInAccount(Entity $entity): bool
    {
        return $this->checkOwnershipAccount($entity);
    }

    
    public function checkIsOwnContact(Entity $entity): bool
    {
        return $this->checkOwnershipContact($entity);
    }
}
