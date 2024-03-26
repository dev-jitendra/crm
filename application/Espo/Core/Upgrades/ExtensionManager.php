<?php


namespace Espo\Core\Upgrades;

class ExtensionManager extends Base
{
    
    protected $name = 'Extension';

    
    protected $params = [
        'packagePath' => 'data/upload/extensions',
        'backupPath' => 'data/.backup/extensions',

        'scriptNames' => [
            'before' => 'BeforeInstall',
            'after' => 'AfterInstall',
            'beforeUninstall' => 'BeforeUninstall',
            'afterUninstall' => 'AfterUninstall',
        ],
        'customDirNames' => [
            'before' => 'beforeInstallFiles',
            'after' => 'afterInstallFiles',
        ],
    ];
}
