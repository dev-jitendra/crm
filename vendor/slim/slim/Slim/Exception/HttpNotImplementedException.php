<?php



declare(strict_types=1);

namespace Slim\Exception;

class HttpNotImplementedException extends HttpSpecializedException
{
    
    protected $code = 501;

    
    protected $message = 'Not implemented.';

    protected string $title = '501 Not Implemented';
    protected string $description = 'The server does not support the functionality required to fulfill the request.';
}
