<?php

declare(strict_types=1);

namespace Doctrine\DBAL\Driver\OCI8\Exception;

use Doctrine\DBAL\Driver\AbstractException;

use function assert;
use function oci_error;


final class Error extends AbstractException
{
    
    public static function new($resource): self
    {
        $error = oci_error($resource);
        assert($error !== false);

        return new self($error['message'], null, $error['code']);
    }
}
