<?php


namespace Espo\Core\Di;

use Espo\Entities\Preferences;

interface PreferencesAware
{
    public function setPreferences(Preferences $preferences): void;
}
