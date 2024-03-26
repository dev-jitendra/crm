<?php

declare(strict_types=1);

namespace Laminas\ServiceManager\Exception;

use InvalidArgumentException as SplInvalidArgumentException;
use Psr\Container\NotFoundExceptionInterface;


class ServiceNotFoundException extends SplInvalidArgumentException implements
    ExceptionInterface,
    NotFoundExceptionInterface
{
}
