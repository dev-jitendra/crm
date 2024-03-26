<?php

declare(strict_types=1);

namespace Doctrine\DBAL\Driver\Mysqli\Exception;

use Doctrine\DBAL\Driver\AbstractException;

use function sprintf;


final class InvalidOption extends AbstractException
{
    
    public static function fromOption(int $option, $value): self
    {
        return new self(
            sprintf('Failed to set option %d with value "%s"', $option, $value),
        );
    }
}
