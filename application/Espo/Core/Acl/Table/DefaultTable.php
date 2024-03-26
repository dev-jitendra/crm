<?php


namespace Espo\Core\Acl\Table;

use Espo\Entities\User;

use Espo\Core\Acl\FieldData;
use Espo\Core\Acl\ScopeData;
use Espo\Core\Acl\Table;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\DataCache;
use Espo\Core\Utils\Metadata;

use stdClass;
use RuntimeException;


class DefaultTable implements Table
{
    private const LEVEL_NOT_SET = 'not-set';

    protected string $type = 'acl';
    protected string $defaultAclType = 'recordAllTeamOwnNo';

    
    private $actionList = [
        self::ACTION_READ,
        self::ACTION_STREAM,
        self::ACTION_EDIT,
        self::ACTION_DELETE,
        self::ACTION_CREATE,
    ];

    
    private $booleanActionList = [
        self::ACTION_CREATE,
    ];

    
    protected $levelList = [
        self::LEVEL_YES,
        self::LEVEL_ALL,
        self::LEVEL_TEAM,
        self::LEVEL_OWN,
        self::LEVEL_NO,
    ];

    
    private $fieldActionList = [
        self::ACTION_READ,
        self::ACTION_EDIT,
    ];

    
    protected $fieldLevelList = [
        self::LEVEL_YES,
        self::LEVEL_NO,
    ];

    private stdClass $data;

    private string $cacheKey;

    
    private $valuePermissionList = [];

    public function __construct(
        private RoleListProvider $roleListProvider,
        CacheKeyProvider $cacheKeyProvider,
        protected User $user,
        Config $config,
        protected Metadata $metadata,
        DataCache $dataCache
    ) {

        $this->data = (object) [
            'scopes' => (object) [],
            'fields' => (object) [],
            'permissions' => (object) [],
        ];

        if (!$this->user->isFetched()) {
            throw new RuntimeException('User must be fetched before ACL check.');
        }

        $this->valuePermissionList = $this->metadata
            ->get(['app', $this->type, 'valuePermissionList'], []);

        $this->cacheKey = $cacheKeyProvider->get();

        if ($config->get('useCache') && $dataCache->has($this->cacheKey)) {
            
            $cachedData = $dataCache->get($this->cacheKey);

            $this->data = $cachedData;
        }
        else {
            $this->load();

            if ($config->get('useCache')) {
                $dataCache->store($this->cacheKey, $this->data);
            }
        }
    }

    
    public function getScopeData(string $scope): ScopeData
    {
        if (!isset($this->data->scopes->$scope)) {
            return ScopeData::fromRaw(false);
        }

        $data = $this->data->scopes->$scope;

        if (is_string($data)) {
            return $this->getScopeData($data);
        }

        return ScopeData::fromRaw($data);
    }

    
    public function getFieldData(string $scope, string $field): FieldData
    {
        if (!isset($this->data->fields->$scope)) {
            return FieldData::fromRaw((object) [
                self::ACTION_READ => self::LEVEL_YES,
                self::ACTION_EDIT => self::LEVEL_YES,
            ]);
        }

        $data = $this->data->fields->$scope->$field ?? (object) [
            self::ACTION_READ => self::LEVEL_YES,
            self::ACTION_EDIT => self::LEVEL_YES,
        ];

        return FieldData::fromRaw($data);
    }

    
    public function getPermissionLevel(string $permission): string
    {
        return $this->data->permissions->$permission ?? self::LEVEL_NO;
    }

    private function load(): void
    {
        $valuePermissionLists = (object) [];

        foreach ($this->valuePermissionList as $permission) {
            $valuePermissionLists->$permission = [];
        }

        $aclTableList = [];
        $fieldTableList = [];

        $aclTable = (object) [];
        $fieldTable = (object) [];

        if (!$this->user->isAdmin()) {
            $roleList = $this->roleListProvider->get();

            foreach ($roleList as $role) {
                $aclTableList[] = $role->getScopeTableData();
                $fieldTableList[] = $role->getFieldTableData();

                foreach ($this->valuePermissionList as $permissionKey) {
                    $permission = $this->normalizePermissionName($permissionKey);

                    $valuePermissionLists->{$permissionKey}[] = $role->getPermissionLevel($permission);
                }
            }

            $aclTable = $this->mergeTableList($aclTableList);
            $fieldTable = $this->mergeFieldTableList($fieldTableList);

            $this->applyDefault($aclTable, $fieldTable);
            $this->applyDisabled($aclTable, $fieldTable);
            $this->applyMandatory($aclTable, $fieldTable);
            $this->applyAdditional($aclTable, $fieldTable, $valuePermissionLists);
        }

        if ($this->user->isAdmin()) {
            $aclTable = (object) [];
            $fieldTable = (object) [];

            $this->applyHighest($aclTable, $fieldTable);
            $this->applyDisabled($aclTable, $fieldTable);
            $this->applyAdminMandatory($aclTable, $fieldTable);
        }

        foreach ($aclTable as $scope => $data) {
            if (is_string($data) && isset($aclTable->$data)) {
                $aclTable->$scope = $aclTable->$data;
            }
        }

        $this->data->scopes = $aclTable;
        $this->data->fields = $fieldTable;

        if (!$this->user->isAdmin()) {
            foreach ($this->valuePermissionList as $permissionKey) {
                $permission = $this->normalizePermissionName($permissionKey);

                $defaultLevel = $this->metadata
                    ->get(['app', $this->type, 'permissionsStrictDefaults', $permissionKey]) ??
                    self::LEVEL_NO;

                $this->data->permissions->$permission = $this->mergeValueList(
                    $valuePermissionLists->$permissionKey,
                    $defaultLevel
                );

                $mandatoryLevel = $this->metadata->get(['app', $this->type, 'mandatory', $permissionKey]);

                if ($mandatoryLevel !== null) {
                    $this->data->permissions->$permission = $mandatoryLevel;
                }
            }
        }

        if ($this->user->isAdmin()) {
            foreach ($this->valuePermissionList as $permissionKey) {
                $permission = $this->normalizePermissionName($permissionKey);

                $highestLevel = $this->metadata
                    ->get(['app', $this->type, 'valuePermissionHighestLevels', $permissionKey]);

                if ($highestLevel !== null) {
                    $this->data->permissions->$permission = $highestLevel;

                    continue;
                }

                $this->data->permissions->$permission = self::LEVEL_ALL;
            }
        }
    }

    private function normalizePermissionName(string $permissionKey): string
    {
        $permission = $permissionKey;

        if (substr($permissionKey, -10) === 'Permission') {
            $permission = substr($permissionKey, 0, -10);
        }

        return $permission;
    }

    protected function applyHighest(stdClass &$table, stdClass &$fieldTable): void
    {
        foreach ($this->getScopeList() as $scope) {
            if ($this->metadata->get(['scopes', $scope, $this->type]) === 'boolean') {
                $table->$scope = true;

                continue;
            }

            if (!$this->metadata->get(['scopes', $scope, 'entity'])) {
                continue;
            }

            $table->$scope = (object) [];

            $actionList = $this->metadata->get(
                ['scopes', $scope, $this->type . 'ActionList'],
                $this->actionList
            );

            $highest = $this->metadata->get(
                ['scopes', $scope, $this->type . 'HighestLevel'],
                self::LEVEL_ALL
            );

            foreach ($actionList as $action) {
                $table->$scope->$action = $highest;

                if (in_array($action, $this->booleanActionList)) {
                    $table->$scope->$action = 'yes';
                }
            }
        }
    }

    protected function applyDefault(stdClass &$table, stdClass &$fieldTable): void
    {
        if ($this->user->isAdmin()) {
            return;
        }

        $data = $this->metadata->get(['app', $this->type, 'strictDefault', 'scopeLevel'], []);

        foreach ($data as $scope => $item) {
            if (isset($table->$scope)) {
                continue;
            }

            $value = $item;

            if (is_array($item)) {
                $value = (object) $item;
            }

            $table->$scope = $value;
        }

        $defaultFieldData = $this->metadata
            ->get(['app', $this->type, 'strictDefault', 'fieldLevel']) ?? [];

        foreach ($this->getScopeList() as $scope) {
            if (isset($table->$scope) && $table->$scope === false) {
                continue;
            }

            if (!$this->metadata->get(['scopes', $scope, 'entity'])) {
                continue;
            }

            $fieldList = array_keys($this->metadata->get(['entityDefs', $scope, 'fields']) ?? []);

            $defaultScopeFieldData = $this->metadata
                ->get(['app', $this->type, 'strictDefault', 'scopeFieldLevel', $scope]) ?? [];

            foreach (array_merge($defaultFieldData, $defaultScopeFieldData) as $field => $f) {
                if (!in_array($field, $fieldList)) {
                    continue;
                }

                if (!isset($fieldTable->$scope)) {
                    $fieldTable->$scope = (object) [];
                }

                if (isset($fieldTable->$scope->$field)) {
                    continue;
                }

                $fieldTable->$scope->$field = (object) [];

                foreach ($this->fieldActionList as $action) {
                    $level = self::LEVEL_NO;

                    if ($f === true) {
                        $level = self::LEVEL_YES;
                    }
                    else {
                        if (is_array($f) && isset($f[$action])) {
                            $level = $f[$action];
                        }
                    }

                    $fieldTable->$scope->$field->$action = $level;
                }
            }
        }

        foreach ($this->getScopeWithAclList() as $scope) {
            if (isset($table->$scope)) {
                continue;
            }

            $aclType = $this->metadata->get(['scopes', $scope, $this->type]);

            if ($aclType === true) {
                $aclType = $this->defaultAclType;
            }

            if (empty($aclType)) {
                continue;
            }

            $defaultValue =
                $this->metadata->get(['app', $this->type, 'scopeLevelTypesStrictDefaults', $aclType]) ??
                $this->metadata->get(['app', $this->type, 'scopeLevelTypesStrictDefaults', 'record']);

            if (is_array($defaultValue)) {
                $defaultValue = (object) $defaultValue;
            }

            $table->$scope = $defaultValue;

            if (is_object($table->$scope)) {
                $actionList = $this->metadata->get(['scopes', $scope, $this->type . 'ActionList']);

                if ($actionList) {
                    foreach (get_object_vars($table->$scope) as $action => $level) {
                        if (!in_array($action, $actionList)) {
                            unset($table->$scope->$action);
                        }
                    }
                }
            }
        }
    }

    protected function applyMandatoryInternal(stdClass $table, stdClass $fieldTable, string $type): void
    {
        $data = $this->metadata->get(['app', $this->type, $type, 'scopeLevel']) ?? [];

        foreach ($data as $scope => $item) {
            $value = $item;

            if (is_array($item)) {
                $value = (object) $item;
            }

            $table->$scope = $value;
        }

        $mandatoryFieldData = $this->metadata->get(['app', $this->type, $type, 'fieldLevel']) ?? [];

        foreach ($this->getScopeList() as $scope) {
            if (isset($table->$scope) && $table->$scope === false) {
                continue;
            }

            if (!$this->metadata->get(['scopes', $scope, 'entity'])) {
                continue;
            }

            $fieldList = array_keys($this->metadata->get(['entityDefs', $scope, 'fields']) ?? []);

            $mandatoryScopeFieldData = $this->metadata
                ->get(['app', $this->type, $type, 'scopeFieldLevel', $scope]) ?? [];

            foreach (array_merge($mandatoryFieldData, $mandatoryScopeFieldData) as $field => $item) {
                if (!in_array($field, $fieldList)) {
                    continue;
                }

                if (!isset($fieldTable->$scope)) {
                    $fieldTable->$scope = (object) [];
                }

                $fieldTable->$scope->$field = (object) [];

                foreach ($this->fieldActionList as $action) {
                    $level = self::LEVEL_NO;

                    if ($item === true) {
                        $level = self::LEVEL_YES;
                    }
                    else {
                        if (is_array($item) && isset($item[$action])) {
                            $level = $item[$action];
                        }
                    }

                    $fieldTable->$scope->$field->$action = $level;
                }
            }
        }
    }

    private function applyMandatory(stdClass $table, stdClass $fieldTable): void
    {
        $this->applyMandatoryInternal($table, $fieldTable, 'mandatory');
    }

    private function applyAdminMandatory(stdClass $table, stdClass $fieldTable): void
    {
        $this->applyMandatoryInternal($table, $fieldTable, 'adminMandatory');
    }

    protected function applyDisabled(stdClass &$table, stdClass &$fieldTable): void
    {
        foreach ($this->getScopeList() as $scope) {
            if ($this->metadata->get(['scopes', $scope, 'disabled'])) {
                $table->$scope = false;

                unset($fieldTable->$scope);
            }
        }
    }

    
    protected function applyAdditional(stdClass &$table, stdClass &$fieldTable, stdClass &$valuePermissionLists): void
    {
        if ($this->user->isPortal()) {
            foreach ($this->getScopeList() as $scope) {
                $table->$scope = false;

                unset($fieldTable->$scope);
            }

            foreach ($this->valuePermissionList as $permission) {
                $valuePermissionLists->{$permission}[] = self::LEVEL_NO;
            }
        }
    }

    
    private function mergeValueList(array $list, string $defaultValue): string
    {
        $result = null;

        foreach ($list as $level) {
            if ($level === self::LEVEL_NOT_SET) {
                continue;
            }

            if (is_null($result)) {
                $result = $level;

                continue;
            }

            if (
                array_search($result, $this->levelList) >
                array_search($level, $this->levelList)
            ) {
                $result = $level;
            }
        }

        if (is_null($result)) {
            $result = $defaultValue;
        }

        return $result;
    }

    
    protected function getScopeWithAclList(): array
    {
        $scopeList = [];

        $scopes = $this->metadata->get('scopes');

        foreach ($scopes as $scope => $d) {
            if (empty($d['acl'])) {
                continue;
            }

            $scopeList[] = $scope;
        }

        return $scopeList;
    }

    
    protected function getScopeList(): array
    {
        $scopeList = [];

        $scopes = $this->metadata->get('scopes');

        foreach ($scopes as $scope => $item) {
            $scopeList[] = $scope;
        }

        return $scopeList;
    }

    
    private function mergeTableList(array $tableList): stdClass
    {
        $data = (object) [];

        $scopeList = $this->getScopeWithAclList();

        foreach ($tableList as $table) {
            foreach ($scopeList as $scope) {
            	if (!isset($table->$scope)) {
                    continue;
                }

                $this->mergeTableListItem($data, $scope, $table->$scope);
            }
        }

        return $data;
    }

    
    private function mergeTableListItem(stdClass $data, string $scope, $row): void
    {
        if ($row === false || $row === null) {
            if (!isset($data->$scope)) {
                $data->$scope = false;
            }

            return;
        }

        if ($row === true) {
            $data->$scope = true;

            return;
        }

        if (!isset($data->$scope)) {
            $data->$scope = (object) [];
        }

        if ($data->$scope === false) {
            $data->$scope = (object) [];
        }

        if (!is_object($row)) {
            return;
        }

        $actionList = $this->metadata
            ->get(['scopes', $scope, $this->type . 'ActionList']) ?? $this->actionList;

        foreach ($actionList as $i => $action) {
            if (isset($row->$action)) {
                $level = $row->$action;

                if (!isset($data->$scope->$action)) {
                    $data->$scope->$action = $level;
                }
                else if (
                    array_search($data->$scope->$action, $this->levelList) >
                    array_search($level, $this->levelList)
                ) {
                    $data->$scope->$action = $level;
                }

                continue;
            }

            if ($i === 0) {
                continue;
            }

            
            $previousAction = $this->actionList[$i - 1];

            if (in_array($action, $this->booleanActionList)) {
                $data->$scope->$action = self::LEVEL_YES;
            }
            else if ($action === self::ACTION_STREAM && isset($data->$scope->$previousAction)) {
                $data->$scope->$action = $data->$scope->$previousAction;
            }
        }
    }

    
    private function mergeFieldTableList(array $tableList): stdClass
    {
        $data = (object) [];

        $scopeList = $this->getScopeWithAclList();

        foreach ($tableList as $table) {
            foreach ($scopeList as $scope) {
                if (!isset($table->$scope)) {
                    continue;
                }

                if (!isset($data->$scope)) {
                    $data->$scope = (object) [];
                }

                if (!is_object($table->$scope)) {
                    continue;
                }

                $fieldList = array_keys($this->metadata->get(['entityDefs', $scope, 'fields']) ?? []);

                foreach (get_object_vars($table->$scope) as $field => $row) {
                    if (!is_object($row)) {
                        continue;
                    }

                    if (!in_array($field, $fieldList)) {
                        continue;
                    }

                    if (!isset($data->$scope->$field)) {
                        $data->$scope->$field = (object) [];
                    }

                    foreach ($this->fieldActionList as $i => $action) {
                        if (!isset($row->$action)) {
                            continue;
                        }

                        $level = $row->$action;

                        if (!isset($data->$scope->$field->$action)) {
                            $data->$scope->$field->$action = $level;
                        }
                        else {
                            if (
                                array_search(
                                    $data->$scope->$field->$action,
                                    $this->fieldLevelList
                                ) > array_search($level, $this->fieldLevelList)
                            ) {
                                $data->$scope->$field->$action = $level;
                            }
                        }
                    }
                }
            }
        }

        return $data;
    }
}
