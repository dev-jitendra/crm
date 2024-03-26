<?php


namespace Espo\Core;

use Espo\ORM\Entity;
use Espo\ORM\EntityManager;

use Espo\Entities\User;

use Espo\Core\Acl\AccessChecker;
use Espo\Core\Acl\AccessChecker\AccessCheckerFactory;
use Espo\Core\Acl\AccessCreateChecker;
use Espo\Core\Acl\AccessDeleteChecker;
use Espo\Core\Acl\AccessEditChecker;
use Espo\Core\Acl\AccessEntityCreateChecker;
use Espo\Core\Acl\AccessEntityDeleteChecker;
use Espo\Core\Acl\AccessEntityEditChecker;
use Espo\Core\Acl\AccessEntityReadChecker;
use Espo\Core\Acl\AccessEntityStreamChecker;
use Espo\Core\Acl\AccessReadChecker;
use Espo\Core\Acl\AccessStreamChecker;
use Espo\Core\Acl\Exceptions\NotImplemented;
use Espo\Core\Acl\GlobalRestriction;
use Espo\Core\Acl\Map\Map;
use Espo\Core\Acl\Map\MapFactory;
use Espo\Core\Acl\OwnershipChecker;
use Espo\Core\Acl\OwnershipChecker\OwnershipCheckerFactory;
use Espo\Core\Acl\OwnershipOwnChecker;
use Espo\Core\Acl\OwnershipTeamChecker;
use Espo\Core\Acl\OwnerUserFieldProvider;
use Espo\Core\Acl\Table;
use Espo\Core\Acl\Table\TableFactory;

use stdClass;
use InvalidArgumentException;


class AclManager
{
    protected const PERMISSION_ASSIGNMENT = 'assignment';

    
    private $accessCheckerHashMap = [];
    
    private $ownershipCheckerHashMap = [];
    
    protected $tableHashMap = [];
    
    protected $mapHashMap = [];
    
    protected $userAclClassName = Acl::class;

    
    private $entityActionInterfaceMap = [
        Table::ACTION_CREATE => AccessEntityCreateChecker::class,
        Table::ACTION_READ => AccessEntityReadChecker::class,
        Table::ACTION_EDIT => AccessEntityEditChecker::class,
        Table::ACTION_DELETE => AccessEntityDeleteChecker::class,
        Table::ACTION_STREAM => AccessEntityStreamChecker::class,
    ];
    
    private $actionInterfaceMap = [
        Table::ACTION_CREATE => AccessCreateChecker::class,
        Table::ACTION_READ => AccessReadChecker::class,
        Table::ACTION_EDIT => AccessEditChecker::class,
        Table::ACTION_DELETE => AccessDeleteChecker::class,
        Table::ACTION_STREAM => AccessStreamChecker::class,
    ];

    
    protected $accessCheckerFactory;
    
    protected $ownershipCheckerFactory;

    
    private $tableFactory;
    
    private $mapFactory;

    public function __construct(
        AccessCheckerFactory $accessCheckerFactory,
        OwnershipCheckerFactory $ownershipCheckerFactory,
        TableFactory $tableFactory,
        MapFactory $mapFactory,
        protected GlobalRestriction $globalRestriction,
        protected OwnerUserFieldProvider $ownerUserFieldProvider,
        protected EntityManager $entityManager
    ) {
        $this->accessCheckerFactory = $accessCheckerFactory;
        $this->ownershipCheckerFactory = $ownershipCheckerFactory;
        $this->tableFactory = $tableFactory;
        $this->mapFactory = $mapFactory;
    }

    
    protected function getAccessChecker(string $scope): AccessChecker
    {
        if (!array_key_exists($scope, $this->accessCheckerHashMap)) {
            $this->accessCheckerHashMap[$scope] = $this->accessCheckerFactory->create($scope, $this);
        }

        return $this->accessCheckerHashMap[$scope];
    }

    
    protected function getOwnershipChecker(string $scope): OwnershipChecker
    {
        if (!array_key_exists($scope, $this->ownershipCheckerHashMap)) {
            $this->ownershipCheckerHashMap[$scope] = $this->ownershipCheckerFactory->create($scope, $this);
        }

        return $this->ownershipCheckerHashMap[$scope];
    }

    protected function getTable(User $user): Table
    {
        $key = $user->hasId() ? $user->getId() : spl_object_hash($user);

        if (!array_key_exists($key, $this->tableHashMap)) {
            $this->tableHashMap[$key] = $this->tableFactory->create($user);
        }

        return $this->tableHashMap[$key];
    }

    protected function getMap(User $user): Map
    {
        $key = $user->hasId() ? $user->getId() : spl_object_hash($user);

        if (!array_key_exists($key, $this->mapHashMap)) {
            $this->mapHashMap[$key] = $this->mapFactory->create($user, $this->getTable($user));
        }

        return $this->mapHashMap[$key];
    }

    
    public function getMapData(User $user): stdClass
    {
        return $this->getMap($user)->getData();
    }

    
    public function getLevel(User $user, string $scope, string $action): string
    {
        if (!$this->checkScope($user, $scope)) {
            return Table::LEVEL_NO;
        }

        $data = $this->getTable($user)->getScopeData($scope);

        return $data->get($action);
    }

    
    public function getPermissionLevel(User $user, string $permission): string
    {
        if (substr($permission, -10) === 'Permission') {
            $permission = substr($permission, 0, -10);
        }

        return $this->getTable($user)->getPermissionLevel($permission);
    }

    
    public function checkReadNo(User $user, string $scope): bool
    {
        return $this->getLevel($user, $scope, Table::ACTION_READ) === Table::LEVEL_NO;
    }

    
    public function checkReadOnlyTeam(User $user, string $scope): bool
    {
        return $this->getLevel($user, $scope, Table::ACTION_READ) === Table::LEVEL_TEAM;
    }

    
    public function checkReadOnlyOwn(User $user, string $scope): bool
    {
        return $this->getLevel($user, $scope, Table::ACTION_READ) === Table::LEVEL_OWN;
    }

    
    public function checkReadAll(User $user, string $scope): bool
    {
        return $this->getLevel($user, $scope, Table::ACTION_READ) === Table::LEVEL_ALL;
    }

    
    public function check(User $user, $subject, ?string $action = null): bool
    {
        if (is_string($subject)) {
            return $this->checkScope($user, $subject, $action);
        }

        
        $entity = $subject;

        if ($entity instanceof Entity) {
            $action = $action ?? Table::ACTION_READ;

            return $this->checkEntity($user, $entity, $action);
        }

        throw new InvalidArgumentException();
    }

    
    public function tryCheck(User $user, $subject, ?string $action = null): bool
    {
        try {
            return $this->check($user, $subject, $action);
        }
        catch (NotImplemented $e) {
            return false;
        }
    }

    
    public function checkEntity(User $user, Entity $entity, string $action = Table::ACTION_READ): bool
    {
        $scope = $entity->getEntityType();

        if (!$this->checkScope($user, $scope, $action)) {
            return false;
        }

        $data = $this->getTable($user)->getScopeData($scope);

        $checker = $this->getAccessChecker($scope);

        $methodName = 'checkEntity' . ucfirst($action);

        $interface = $this->entityActionInterfaceMap[$action] ?? null;

        if ($interface && $checker instanceof $interface) {
            return $checker->$methodName($user, $entity, $data);
        }

        if (method_exists($checker, $methodName)) {
            
            return $checker->$methodName($user, $entity, $data);
        }

        if (method_exists($checker, 'checkEntity')) {
            
            return $checker->checkEntity($user, $entity, $data, $action);
        }

        throw new NotImplemented("No entity access checker for '{$scope}' action '{$action}'.");
    }

    
    public function checkEntityRead(User $user, Entity $entity): bool
    {
        return $this->checkEntity($user, $entity, Table::ACTION_READ);
    }

    
    public function checkEntityCreate(User $user, Entity $entity): bool
    {
        return $this->checkEntity($user, $entity, Table::ACTION_CREATE);
    }

    
    public function checkEntityEdit(User $user, Entity $entity): bool
    {
        return $this->checkEntity($user, $entity, Table::ACTION_EDIT);
    }

    
    public function checkEntityDelete(User $user, Entity $entity): bool
    {
        return $this->checkEntity($user, $entity, Table::ACTION_DELETE);
    }

    
    public function checkEntityStream(User $user, Entity $entity): bool
    {
        return $this->checkEntity($user, $entity, Table::ACTION_STREAM);
    }

    
    public function checkOwnershipOwn(User $user, Entity $entity): bool
    {
        $checker = $this->getOwnershipChecker($entity->getEntityType());

        if (!$checker instanceof OwnershipOwnChecker) {
            return false;
        }

        return $checker->checkOwn($user, $entity);
    }

    
    public function checkOwnershipTeam(User $user, Entity $entity): bool
    {
        $checker = $this->getOwnershipChecker($entity->getEntityType());

        if (!$checker instanceof OwnershipTeamChecker) {
            return false;
        }

        return $checker->checkTeam($user, $entity);
    }

    
    public function checkScope(User $user, string $scope, ?string $action = null): bool
    {
        if ($action && !$this->checkScope($user, $scope)) {
            return false;
        }

        $data = $this->getTable($user)->getScopeData($scope);

        $checker = $this->getAccessChecker($scope);

        if (!$action) {
            return $checker->check($user, $data);
        }

        $methodName = 'check' . ucfirst($action);

        $interface = $this->actionInterfaceMap[$action] ?? null;

        if ($interface && $checker instanceof $interface) {
            return $checker->$methodName($user, $data);
        }

        
        $methodName = 'checkScope';

        if (!method_exists($checker, $methodName)) {
            throw new NotImplemented("No access checker for '{$scope}' action '{$action}'.");
        }

        return $checker->$methodName($user, $data, $action);
    }

    
    protected function getGlobalRestrictionTypeList(User $user, string $action = Table::ACTION_READ): array
    {
        $typeList = [
            GlobalRestriction::TYPE_FORBIDDEN,
        ];

        if ($action === Table::ACTION_READ) {
            $typeList[] = GlobalRestriction::TYPE_INTERNAL;
        }

        if (!$user->isAdmin()) {
            $typeList[] = GlobalRestriction::TYPE_ONLY_ADMIN;
        }

        if ($action === Table::ACTION_EDIT) {
            $typeList[] = GlobalRestriction::TYPE_READ_ONLY;

            if (!$user->isAdmin()) {
                $typeList[] = GlobalRestriction::TYPE_NON_ADMIN_READ_ONLY;
            }
        }

        return $typeList;
    }

    
    public function getScopeForbiddenAttributeList(
        User $user,
        string $scope,
        string $action = Table::ACTION_READ,
        string $thresholdLevel = Table::LEVEL_NO
    ): array {

        $list = array_merge(
            $this->getMap($user)->getScopeForbiddenAttributeList(
                $scope,
                $action,
                $thresholdLevel
            ),
            $this->getScopeRestrictedAttributeList(
                $scope,
                $this->getGlobalRestrictionTypeList($user, $action)
            )
        );

        return array_unique($list);
    }

    
    public function getScopeForbiddenFieldList(
        User $user,
        string $scope,
        string $action = Table::ACTION_READ,
        string $thresholdLevel = Table::LEVEL_NO
    ): array {

        $list = array_merge(
            $this->getMap($user)->getScopeForbiddenFieldList(
                $scope,
                $action,
                $thresholdLevel
            ),
            $this->getScopeRestrictedFieldList(
                $scope,
                $this->getGlobalRestrictionTypeList($user, $action)
            )
        );

        return array_unique($list);
    }

    
    public function getScopeForbiddenLinkList(
        User $user,
        string $scope,
        string $action = Table::ACTION_READ,
        string $thresholdLevel = Table::LEVEL_NO
    ): array {

        return $this->getScopeRestrictedLinkList(
            $scope,
            $this->getGlobalRestrictionTypeList($user, $action)
        );
    }

    
    public function checkField(User $user, string $scope, string $field, string $action = Table::ACTION_READ): bool
    {
        return !in_array($field, $this->getScopeForbiddenFieldList($user, $scope, $action));
    }

    
    public function checkUserPermission(User $user, $target, string $permissionType = 'user'): bool
    {
        $permission = $this->getPermissionLevel($user, $permissionType);

        if (is_object($target)) {
            $userId = $target->getId();
        }
        else {
            $userId = $target;
        }

        if ($user->getId() === $userId) {
            return true;
        }

        if ($permission === Table::LEVEL_NO) {
            return false;
        }

        if ($permission === Table::LEVEL_YES) {
            return true;
        }

        if ($permission === Table::LEVEL_TEAM) {
            
            $teamIdList = $user->getLinkMultipleIdList('teams');

            
            $userRepository = $this->entityManager->getRepository(User::ENTITY_TYPE);

            if (!$userRepository->checkBelongsToAnyOfTeams($userId, $teamIdList)) {
                return false;
            }
        }

        return true;
    }

    
    public function checkAssignmentPermission(User $user, $target): bool
    {
        return $this->checkUserPermission($user, $target, self::PERMISSION_ASSIGNMENT);
    }

    
    public function createUserAcl(User $user): Acl
    {
        $className = $this->userAclClassName;

        $acl = new $className($this, $user);

        
        return $acl;
    }

    
    public function getScopeRestrictedFieldList(string $scope, $type): array
    {
        if (is_array($type)) {
            $typeList = $type;

            $list = [];

            foreach ($typeList as $type) {
                $list = array_merge(
                    $list,
                    $this->globalRestriction->getScopeRestrictedFieldList($scope, $type)
                );
            }

            return array_unique($list);
        }

        return $this->globalRestriction->getScopeRestrictedFieldList($scope, $type);
    }

    
    public function getScopeRestrictedAttributeList(string $scope, $type): array
    {
        if (is_array($type)) {
            $typeList = $type;

            $list = [];

            foreach ($typeList as $type) {
                $list = array_merge(
                    $list,
                    $this->globalRestriction->getScopeRestrictedAttributeList($scope, $type)
                );
            }

            return array_unique($list);
        }

        return $this->globalRestriction->getScopeRestrictedAttributeList($scope, $type);
    }

    
    public function getScopeRestrictedLinkList(string $scope, $type): array
    {
        if (is_array($type)) {
            $typeList = $type;

            $list = [];

            foreach ($typeList as $type) {
                $list = array_merge(
                    $list,
                    $this->globalRestriction->getScopeRestrictedLinkList($scope, $type)
                );
            }

            return array_unique($list);
        }

        return $this->globalRestriction->getScopeRestrictedLinkList($scope, $type);
    }

    
    public function getReadOwnerUserField(string $entityType): ?string
    {
        return $this->ownerUserFieldProvider->get($entityType);
    }

    
    public function checkIsOwner(User $user, Entity $entity): bool
    {
        return $this->checkOwnershipOwn($user, $entity);
    }

    
    public function checkInTeam(User $user, Entity $entity): bool
    {
        return $this->checkOwnershipTeam($user, $entity);
    }

    
    public function getImplementation(string $scope): object
    {
        return $this->getAccessChecker($scope);
    }

    
    public function get(User $user, string $permission): string
    {
        return $this->getPermissionLevel($user, $permission);
    }

    
    public function checkUser(User $user, string $permission, User $target): bool
    {
        if ($this->getPermissionLevel($user, $permission) === Table::LEVEL_ALL) {
            return true;
        }

        if ($this->getPermissionLevel($user, $permission) === Table::LEVEL_NO) {
            if ($target->getId() === $user->getId()) {
                return true;
            }

            return false;
        }

        if ($this->get($user, $permission) === Table::LEVEL_TEAM) {
            if ($target->getId() === $user->getId()) {
                return true;
            }

            $targetTeamIdList = $target->getTeamIdList();

            $inTeam = false;

            foreach ($user->getTeamIdList() as $id) {
                if (in_array($id, $targetTeamIdList)) {
                    $inTeam = true;

                    break;
                }
            }

            if ($inTeam) {
                return true;
            }

            return false;
        }

        return false;
    }
}
