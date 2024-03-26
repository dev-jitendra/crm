<?php


namespace Espo\Core\Api;

use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\Conflict;
use Espo\Core\Exceptions\Error;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Exceptions\HasBody;
use Espo\Core\Exceptions\HasLogLevel;
use Espo\Core\Exceptions\HasLogMessage;
use Espo\Core\Exceptions\NotFound;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\Log;

use Throwable;


class ErrorOutput
{
    
    private $errorDescriptions = [
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Page Not Found',
        409 => 'Conflict',
        500 => 'Internal Server Error',
        503 => 'Service Unavailable',
    ];

    
    private $allowedStatusCodeList = [
        200,
        201,
        400,
        401,
        403,
        404,
        409,
        500,
        503,
    ];

    
    private array $printStatusReasonExceptionClassNameList = [
        Error::class,
        Forbidden::class,
        Conflict::class,
        BadRequest::class,
        NotFound::class,
    ];

    public function __construct(private Log $log, private Config $config)
    {}

    public function process(
        Request $request,
        Response $response,
        Throwable $exception,
        ?string $route = null
    ): void {

        $this->processInternal($request, $response, $exception, $route, false);
    }

    public function processWithBodyPrinting(
        Request $request,
        Response $response,
        Throwable $exception,
        ?string $route = null
    ): void {

        $this->processInternal($request, $response, $exception, $route, true);
    }

    private function processInternal(
        Request $request,
        Response $response,
        Throwable $exception,
        ?string $route = null,
        bool $toPrintBody = false
    ): void {

        $message = $exception->getMessage();
        $statusCode = $exception->getCode();

        if ($exception instanceof HasLogMessage) {
            $message = $exception->getLogMessage();
        }

        if ($route) {
            $this->processRoute($route, $request, $exception);
        }

        $logLevel = $exception instanceof HasLogLevel ?
            $exception->getLogLevel() :
            Log::LEVEL_ERROR;

        $messageLineFile =
            'line: ' . $exception->getLine() . ', ' .
            'file: ' . $exception->getFile();

        $logMessageItemList = [];

        if ($message) {
            $logMessageItemList[] = "{$message}";
        }

        $logMessageItemList[] = $request->getMethod() . ' ' . $request->getResourcePath();
        $logMessageItemList[] = $messageLineFile;

        $logMessage = "($statusCode) " . implode("; ", $logMessageItemList);

        if ($this->toPrintTrace()) {
            $logMessage .= " :: " . $exception->getTraceAsString();
        }

        $this->log->log($logLevel, $logMessage);

        if (!in_array($statusCode, $this->allowedStatusCodeList)) {
            $statusCode = 500;
        }

        $response->setStatus($statusCode);

        if ($this->toPrintExceptionStatusReason($exception)) {
            $response->setHeader('X-Status-Reason', $this->stripInvalidCharactersFromHeaderValue($message));
        }

        if ($exception instanceof HasBody && $this->exceptionHasBody($exception)) {
            $response->writeBody($exception->getBody() ?? '');

            $toPrintBody = false;
        }

        if ($toPrintBody) {
            $codeDescription = $this->getCodeDescription($statusCode);

            $statusText = isset($codeDescription) ?
                $statusCode . ' '. $codeDescription :
                'HTTP ' . $statusCode;

            if ($message) {
                $message = htmlspecialchars($message);
            }

            $response->writeBody(self::generateErrorBody($statusText, $message));
        }
    }

    private function exceptionHasBody(Throwable $exception): bool
    {
        if (!$exception instanceof HasBody) {
            return false;
        }

        $exceptionBody = $exception->getBody();

        return $exceptionBody !== null;
    }

    private function getCodeDescription(int $statusCode): ?string
    {
        if (isset($this->errorDescriptions[$statusCode])) {
            return $this->errorDescriptions[$statusCode];
        }

        return null;
    }

    private function clearPasswords(string $string): string
    {
        return preg_replace('/"(.*password.*)":".*"/i', '"$1":"*****"', $string) ?? $string;
    }

    private static function generateErrorBody(string $header, string $text): string
    {
        $body = "<h1>" . $header . "</h1>";
        $body .= $text;

        return $body;
    }

    private function stripInvalidCharactersFromHeaderValue(string $value): string
    {
        $pattern = "/[^ \t\x21-\x7E\x80-\xFF]/";

        
        return preg_replace($pattern, ' ', $value);
    }

    private function processRoute(string $route, Request $request, Throwable $exception): void
    {
        $requestBodyString = $this->clearPasswords($request->getBodyContents() ?? '');

        $message = $exception->getMessage();
        $statusCode = $exception->getCode();

        $routeParams = $request->getRouteParams();

        $logMessage = "API ($statusCode) ";

        $logMessageItemList = [];

        if ($message) {
            $logMessageItemList[] = $message;
        }

        $logMessageItemList[] = $request->getMethod() . ' ' . $request->getResourcePath();

        if ($requestBodyString) {
            $logMessageItemList[] = "Input data: " . $requestBodyString;
        }

        $logMessageItemList[] = "Route pattern: " . $route;

        if (!empty($routeParams)) {
            $logMessageItemList[] = "Route params: " . print_r($routeParams, true);
        }

        $logMessage .= implode("; ", $logMessageItemList);

        $this->log->log('debug', $logMessage);
    }

    private function toPrintTrace(): bool
    {
        return (bool) $this->config->get('logger.printTrace');
    }

    private function toPrintExceptionStatusReason(Throwable $exception): bool
    {
        foreach ($this->printStatusReasonExceptionClassNameList as $clasName) {

            if ($exception instanceof ($clasName)) {
                return true;
            }
        }

        return false;
    }
}
