<?php



declare(strict_types=1);

namespace Slim\Interfaces;

use Psr\Http\Message\ServerRequestInterface;

interface ServerRequestCreatorInterface
{
    public function createServerRequestFromGlobals(): ServerRequestInterface;
}
