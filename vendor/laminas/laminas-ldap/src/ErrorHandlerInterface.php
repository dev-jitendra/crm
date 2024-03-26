<?php

namespace Laminas\Ldap;

use const E_WARNING;


interface ErrorHandlerInterface
{
    
    public function startErrorHandling($level = E_WARNING);

    
    public function stopErrorHandling($throw = false);
}
