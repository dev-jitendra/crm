<?php


namespace Espo\Core\Upgrades;

class UpgradeManager extends Base
{
    
    protected $name = 'Upgrade';

    
    protected $params = [
        'packagePath' => 'data/upload/upgrades',
        'backupPath' => 'data/.backup/upgrades',
        'scriptNames' => [
            'before' => 'BeforeUpgrade',
            'after' => 'AfterUpgrade',
        ],
        'customDirNames' => [
            'before' => 'beforeUpgradeFiles',
            'after' => 'afterUpgradeFiles',
            'vendor' => 'vendorFiles',
        ],
    ];
}
