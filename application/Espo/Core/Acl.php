<?php


namespace Espo\Core;

use Espo\Core\Acl\Exceptions\NotImplemented;
use Espo\Core\Acl\GlobalRestriction;
use Espo\Core\Acl\Table;

use Espo\ORM\Entity;
use Espo\Entities\User;

use stdClass;


class Acl
{
    public function __construct(
        protected AclManager $aclManager,
        protected User $user
    ) {}

    
    public function getMapData(): stdClass
    {
        return $this->aclManager->getMapData($this->user);
    }

    
    public function getLevel(string $scope, string $action): string
    {
        return $this->aclManager->getLevel($this->user, $scope, $action);
    }

    
    public function getPermissionLevel(string $permission): string
    {
        return $this->aclManager->getPermissionLevel($this->user, $permission);
    }

    
    public function checkReadNo(string $scope): bool
    {
        return $this->aclManager->checkReadNo($this->user, $scope);
    }

    
    public function checkReadOnlyTeam(string $scope): bool
    {
        return $this->aclManager->checkReadOnlyTeam($this->user, $scope);
    }

    
    public function checkReadOnlyOwn(string $scope): bool
    {
        return $this->aclManager->checkReadOnlyOwn($this->user, $scope);
    }

    
    public function checkReadAll(string $scope): bool
    {
        return $this->aclManager->checkReadAll($this->user, $scope);
    }

    
    public function check($subject, ?string $action = null): bool
    {
        return $this->aclManager->check($this->user, $subject, $action);
    }

    
    public function tryCheck($subject, ?string $action = null): bool
    {
        return $this->aclManager->tryCheck($this->user, $subject, $action);
    }

    
    public function checkScope(string $scope, ?string $action = null): bool
    {
        return $this->aclManager->checkScope($this->user, $scope, $action);
    }

    
    public function checkEntity(Entity $entity, string $action = Table::ACTION_READ): bool
    {
        return $this->aclManager->checkEntity($this->user, $entity, $action);
    }

    
    public function checkEntityRead(Entity $entity): bool
    {
        return $this->checkEntity($entity, Table::ACTION_READ);
    }

    
    public function checkEntityCreate(Entity $entity): bool
    {
        return $this->checkEntity($entity, Table::ACTION_CREATE);
    }

    
    public function checkEntityEdit(Entity $entity): bool
    {
        return $this->checkEntity($entity, Table::ACTION_EDIT);
    }

    
    public function checkEntityDelete(Entity $entity): bool
    {
        return $this->checkEntity($entity, Table::ACTION_DELETE);
    }

    
    public function checkEntityStream(Entity $entity): bool
    {
        return $this->checkEntity($entity, Table::ACTION_STREAM);
    }

    
    public function checkOwnershipOwn(Entity $entity): bool
    {
        return $this->aclManager->checkOwnershipOwn($this->user, $entity);
    }

    
    public function checkOwnershipTeam(Entity $entity): bool
    {
        return $this->aclManager->checkOwnershipTeam($this->user, $entity);
    }

    
    public function getScopeForbiddenAttributeList(
        string $scope,
        string $action = Table::ACTION_READ,
        string $thresholdLevel = Table::LEVEL_NO
    ): array {

        return $this->aclManager
            ->getScopeForbiddenAttributeList($this->user, $scope, $action, $thresholdLevel);
    }

    
    public function getScopeForbiddenFieldList(
        string $scope,
        string $action = Table::ACTION_READ,
        string $thresholdLevel = Table::LEVEL_NO
    ): array {

        return $this->aclManager
            ->getScopeForbiddenFieldList($this->user, $scope, $action, $thresholdLevel);
    }

    
    public function checkField(string $scope, string $field, string $action = Table::ACTION_READ): bool
    {
        return $this->aclManager->checkField($this->user, $scope, $field, $action);
    }

    
    public function getScopeForbiddenLinkList(
        string $scope,
        string $action = Table::ACTION_READ,
        string $thresholdLevel = Table::LEVEL_NO
    ): array {

        return $this->aclManager->getScopeForbiddenLinkList($this->user, $scope, $action, $thresholdLevel);
    }

    
    public function checkUserPermission($target, string $permissionType = 'user'): bool
    {
        return $this->aclManager->checkUserPermission($this->user, $target, $permissionType);
    }

    
    public function checkAssignmentPermission($target): bool
    {
        return $this->aclManager->checkAssignmentPermission($this->user, $target);
    }

    
    public function getScopeRestrictedFieldList(string $scope, $type): array
    {
        return $this->aclManager->getScopeRestrictedFieldList($scope, $type);
    }

    
    public function getScopeRestrictedAttributeList(string $scope, $type): array
    {
        return $this->aclManager->getScopeRestrictedAttributeList($scope, $type);
    }

    
    public function getScopeRestrictedLinkList(string $scope, $type): array
    {
        return $this->aclManager->getScopeRestrictedLinkList($scope, $type);
    }

    
    public function get(string $permission): string
    {
        return $this->getPermissionLevel($permission);
    }

    
    public function checkIsOwner(Entity $entity): bool
    {
        return $this->aclManager->checkOwnershipOwn($this->user, $entity);
    }

    
    public function checkInTeam(Entity $entity): bool
    {
        return $this->aclManager->checkOwnershipTeam($this->user, $entity);
    }

    
    public function checkUser(string $permission, User $entity): bool
    {
        return $this->aclManager->checkUser($this->user, $permission, $entity);
    }
}
