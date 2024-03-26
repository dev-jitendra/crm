<?php

namespace Laminas\Crypt\Exception;

use DomainException;
use Psr\Container\NotFoundExceptionInterface as PsrNotFoundException;


class NotFoundException extends DomainException implements PsrNotFoundException
{
}
