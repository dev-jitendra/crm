<?php

declare(strict_types=1);

namespace AsyncAws\Core\Credentials;

use AsyncAws\Core\Configuration;


final class NullProvider implements CredentialProvider
{
    public function getCredentials(Configuration $configuration): ?Credentials
    {
        return null;
    }
}
