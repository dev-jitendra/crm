<?php


namespace Espo\Core\Container\Exceptions;

use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;


class NotFoundException extends RuntimeException implements NotFoundExceptionInterface
{}
