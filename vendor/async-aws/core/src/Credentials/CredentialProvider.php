<?php

declare(strict_types=1);

namespace AsyncAws\Core\Credentials;

use AsyncAws\Core\Configuration;


interface CredentialProvider
{
    
    public function getCredentials(Configuration $configuration): ?Credentials;
}
