<?php



declare(strict_types=1);

namespace Slim;

use Psr\Log\AbstractLogger;
use Psr\Log\InvalidArgumentException;
use Stringable;

use function error_log;

class Logger extends AbstractLogger
{
    
    public function log($level, $message, array $context = []): void
    {
        error_log((string) $message);
    }
}
