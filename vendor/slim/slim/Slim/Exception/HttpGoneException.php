<?php



declare(strict_types=1);

namespace Slim\Exception;

class HttpGoneException extends HttpSpecializedException
{
    
    protected $code = 410;

    
    protected $message = 'Gone.';

    protected string $title = '410 Gone';
    protected string $description = 'The target resource is no longer available at the origin server.';
}
