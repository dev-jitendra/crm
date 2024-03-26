<?php


class Utils
{
    static public $actionPath = 'install/core/actions';

    static public function checkActionExists(string $actionName): bool
    {
        return in_array($actionName, [
            'saveSettings',
            'buildDatabase',
            'checkPermission',
            'createUser',
            'errors',
            'finish',
            'main',
            'saveEmailSettings',
            'savePreferences',
            'settingsTest',
            'setupConfirmation',
            'step1',
            'step2',
            'step3',
            'step4',
            'step5',
        ]);
    }
}
