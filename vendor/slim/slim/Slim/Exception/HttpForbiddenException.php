<?php



declare(strict_types=1);

namespace Slim\Exception;

class HttpForbiddenException extends HttpSpecializedException
{
    
    protected $code = 403;

    
    protected $message = 'Forbidden.';

    protected string $title = '403 Forbidden';
    protected string $description = 'You are not permitted to perform the requested operation.';
}
