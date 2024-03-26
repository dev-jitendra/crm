<?php



declare(strict_types=1);

namespace Slim\Exception;

class HttpBadRequestException extends HttpSpecializedException
{
    
    protected $code = 400;

    
    protected $message = 'Bad request.';

    protected string $title = '400 Bad Request';
    protected string $description = 'The server cannot or will not process ' .
        'the request due to an apparent client error.';
}
