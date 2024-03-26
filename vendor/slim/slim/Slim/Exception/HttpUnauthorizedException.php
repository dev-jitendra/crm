<?php



declare(strict_types=1);

namespace Slim\Exception;

class HttpUnauthorizedException extends HttpSpecializedException
{
    
    protected $code = 401;

    
    protected $message = 'Unauthorized.';

    protected string $title = '401 Unauthorized';
    protected string $description = 'The request requires valid user authentication.';
}
