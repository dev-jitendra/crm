<?php

declare(strict_types=1);

namespace Laminas\ServiceManager\Exception;

use RuntimeException as SplRuntimeException;


class InvalidServiceException extends SplRuntimeException implements ExceptionInterface
{
}
