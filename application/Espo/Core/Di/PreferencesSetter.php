<?php


namespace Espo\Core\Di;

use Espo\Entities\Preferences;

trait PreferencesSetter
{
    
    protected $preferences;

    public function setPreferences(Preferences $preferences): void
    {
        $this->preferences = $preferences;
    }
}
