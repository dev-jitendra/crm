<?php

declare(strict_types=1);

namespace Doctrine\DBAL\Driver\IBMDB2\Exception;

use Doctrine\DBAL\Driver\AbstractException;


final class CannotCopyStreamToStream extends AbstractException
{
    
    public static function new(?array $error): self
    {
        $message = 'Could not copy source stream to temporary file';

        if ($error !== null) {
            $message .= ': ' . $error['message'];
        }

        return new self($message);
    }
}
