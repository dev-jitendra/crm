<?php


namespace Espo\Core\Portal\Acl;

use Espo\Core\Acl\Table\DefaultTable as BaseTable;

use stdClass;

class Table extends BaseTable
{
    public const LEVEL_ACCOUNT = 'account';
    public const LEVEL_CONTACT = 'contact';

    protected string $type = 'aclPortal';
    protected string $defaultAclType = 'recordAllOwnNo';

    
    protected $levelList = [
        self::LEVEL_YES,
        self::LEVEL_ALL,
        self::LEVEL_ACCOUNT.
        self::LEVEL_CONTACT,
        self::LEVEL_OWN,
        self::LEVEL_NO,
    ];

    
    protected function getScopeWithAclList(): array
    {
        $scopeList = [];

        $scopes = $this->metadata->get('scopes');

        foreach ($scopes as $scope => $item) {
            if (empty($item['acl'])) {
                continue;
            }

            if (empty($item['aclPortal'])) {
                continue;
            }

            $scopeList[] = $scope;
        }

        return $scopeList;
    }

    protected function applyDefault(stdClass &$table, stdClass &$fieldTable): void
    {
        parent::applyDefault($table, $fieldTable);

        foreach ($this->getScopeList() as $scope) {
            if (!isset($table->$scope)) {
                $table->$scope = false;
            }
        }
    }

    protected function applyDisabled(stdClass &$table, stdClass &$fieldTable): void
    {
        foreach ($this->getScopeList() as $scope) {
            $item = $this->metadata->get(['scopes', $scope]) ?? [];

            if (!empty($item['disabled']) || !empty($item['portalDisabled'])) {
                $table->$scope = false;

                unset($fieldTable->$scope);
            }
        }
    }

    protected function applyAdditional(stdClass &$table, stdClass &$fieldTable, stdClass &$valuePermissionLists): void
    {
    }
}
