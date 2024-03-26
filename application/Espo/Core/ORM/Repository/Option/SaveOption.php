<?php


namespace Espo\Core\ORM\Repository\Option;

use Espo\ORM\Repository\Option\SaveOption as BaseSaveOption;


class SaveOption
{
    
    public const SILENT = 'silent';
    
    public const IMPORT = 'import';
    
    public const API = 'api';
    
    public const SKIP_ALL = BaseSaveOption::SKIP_ALL;
    
    public const KEEP_NEW = BaseSaveOption::KEEP_NEW;
    
    public const KEEP_DIRTY = BaseSaveOption::KEEP_DIRTY;
    
    public const SKIP_HOOKS = 'skipHooks';
    
    public const SKIP_CREATED_BY = 'skipCreatedBy';
    
    public const SKIP_MODIFIED_BY = 'skipModifiedBy';
    
    public const CREATED_BY_ID = 'createdById';
    
    public const MODIFIED_BY_ID = 'modifiedById';
}
