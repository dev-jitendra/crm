<?php


namespace Espo\Core\Acl;


interface Table
{
    public const LEVEL_YES = 'yes';
    public const LEVEL_NO = 'no';
    public const LEVEL_ALL = 'all';
    public const LEVEL_TEAM = 'team';
    public const LEVEL_OWN = 'own';

    public const ACTION_READ = 'read';
    public const ACTION_STREAM = 'stream';
    public const ACTION_EDIT = 'edit';
    public const ACTION_DELETE = 'delete';
    public const ACTION_CREATE = 'create';

    
    public function getScopeData(string $scope): ScopeData;

    
    public function getFieldData(string $scope, string $field): FieldData;

    
    public function getPermissionLevel(string $permission): string;
}
