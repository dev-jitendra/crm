<?php


namespace Espo\Core\Di;

use Espo\Core\Utils\EmailFilterManager;

interface EmailFilterManagerAware
{
    public function setEmailFilterManager(EmailFilterManager $emailFilterManager): void;
}
