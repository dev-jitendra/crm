<?php


namespace Espo\Core\Di;

use Espo\Core\Utils\EmailFilterManager;

trait EmailFilterManagerSetter
{
    
    protected $emailFilterManager;

    public function setEmailFilterManager(EmailFilterManager $emailFilterManager): void
    {
        $this->emailFilterManager = $emailFilterManager;
    }
}
