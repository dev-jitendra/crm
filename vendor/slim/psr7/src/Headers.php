<?php



declare(strict_types=1);

namespace Slim\Psr7;

use InvalidArgumentException;
use Slim\Psr7\Interfaces\HeadersInterface;

use function base64_encode;
use function function_exists;
use function getallheaders;
use function is_array;
use function is_numeric;
use function is_string;
use function preg_match;
use function strpos;
use function strtolower;
use function strtr;
use function substr;
use function trim;

class Headers implements HeadersInterface
{
    protected array $globals;

    
    protected array $headers;

    
    final public function __construct(array $headers = [], ?array $globals = null)
    {
        $this->globals = $globals ?? $_SERVER;
        $this->setHeaders($headers);
    }

    
    public function addHeader($name, $value): HeadersInterface
    {
        [$values, $originalName, $normalizedName] = $this->prepareHeader($name, $value);

        if (isset($this->headers[$normalizedName])) {
            $header = $this->headers[$normalizedName];
            $header->addValues($values);
        } else {
            $this->headers[$normalizedName] = new Header($originalName, $normalizedName, $values);
        }

        return $this;
    }

    
    public function removeHeader(string $name): HeadersInterface
    {
        $name = $this->normalizeHeaderName($name);
        unset($this->headers[$name]);
        return $this;
    }

    
    public function getHeader(string $name, $default = []): array
    {
        $name = $this->normalizeHeaderName($name);

        if (isset($this->headers[$name])) {
            $header = $this->headers[$name];
            return $header->getValues();
        }

        if (empty($default)) {
            return $default;
        }

        $this->validateHeader($name, $default);
        return $this->trimHeaderValue($default);
    }

    
    public function setHeader($name, $value): HeadersInterface
    {
        [$values, $originalName, $normalizedName] = $this->prepareHeader($name, $value);

        
        if (isset($this->headers[$normalizedName])) {
            $existingHeader = $this->headers[$normalizedName];
            $originalName = $existingHeader->getOriginalName();
        }

        $this->headers[$normalizedName] = new Header($originalName, $normalizedName, $values);

        return $this;
    }

    
    public function setHeaders(array $headers): HeadersInterface
    {
        $this->headers = [];

        foreach ($this->parseAuthorizationHeader($headers) as $name => $value) {
            $this->addHeader($name, $value);
        }

        return $this;
    }

    
    public function hasHeader(string $name): bool
    {
        $name = $this->normalizeHeaderName($name);
        return isset($this->headers[$name]);
    }

    
    public function getHeaders(bool $originalCase = false): array
    {
        $headers = [];

        foreach ($this->headers as $header) {
            $name = $originalCase ? $header->getOriginalName() : $header->getNormalizedName();
            $headers[$name] = $header->getValues();
        }

        return $headers;
    }

    
    protected function normalizeHeaderName(string $name, bool $preserveCase = false): string
    {
        $name = strtr($name, '_', '-');

        if (!$preserveCase) {
            $name = strtolower($name);
        }

        if (strpos(strtolower($name), 'http-') === 0) {
            $name = substr($name, 5);
        }

        return $name;
    }

    
    protected function parseAuthorizationHeader(array $headers): array
    {
        $hasAuthorizationHeader = false;
        foreach ($headers as $name => $value) {
            if (strtolower((string) $name) === 'authorization') {
                $hasAuthorizationHeader = true;
                break;
            }
        }

        if (!$hasAuthorizationHeader) {
            if (isset($this->globals['REDIRECT_HTTP_AUTHORIZATION'])) {
                $headers['Authorization'] = $this->globals['REDIRECT_HTTP_AUTHORIZATION'];
            } elseif (isset($this->globals['PHP_AUTH_USER'])) {
                $pw = $this->globals['PHP_AUTH_PW'] ?? '';
                $headers['Authorization'] = 'Basic ' . base64_encode($this->globals['PHP_AUTH_USER'] . ':' . $pw);
            } elseif (isset($this->globals['PHP_AUTH_DIGEST'])) {
                $headers['Authorization'] = $this->globals['PHP_AUTH_DIGEST'];
            }
        }

        return $headers;
    }

    
    protected function trimHeaderValue($value): array
    {
        $items = is_array($value) ? $value : [$value];
        $result = [];
        foreach ($items as $item) {
            $result[] = trim((string) $item, " \t");
        }
        return $result;
    }

    
    protected function prepareHeader($name, $value): array
    {
        $this->validateHeader($name, $value);
        $values = $this->trimHeaderValue($value);
        $originalName = $this->normalizeHeaderName($name, true);
        $normalizedName = $this->normalizeHeaderName($name);
        return [$values, $originalName, $normalizedName];
    }

    
    protected function validateHeader($name, $value): void
    {
        $this->validateHeaderName($name);
        $this->validateHeaderValue($value);
    }

    
    protected function validateHeaderName($name): void
    {
        if (!is_string($name) || preg_match("@^[!#$%&'*+.^_`|~0-9A-Za-z-]+$@D", $name) !== 1) {
            throw new InvalidArgumentException('Header name must be an RFC 7230 compatible string.');
        }
    }

    
    protected function validateHeaderValue($value): void
    {
        $items = is_array($value) ? $value : [$value];

        if (empty($items)) {
            throw new InvalidArgumentException(
                'Header values must be a string or an array of strings, empty array given.'
            );
        }

        $pattern = "@^[ \t\x21-\x7E\x80-\xFF]*$@D";
        foreach ($items as $item) {
            $hasInvalidType = !is_numeric($item) && !is_string($item);
            $rejected = $hasInvalidType || preg_match($pattern, (string) $item) !== 1;
            if ($rejected) {
                throw new InvalidArgumentException(
                    'Header values must be RFC 7230 compatible strings.'
                );
            }
        }
    }

    
    public static function createFromGlobals()
    {
        $headers = null;

        if (function_exists('getallheaders')) {
            $headers = getallheaders();
        }

        if (!is_array($headers)) {
            $headers = [];
        }

        return new static($headers);
    }
}
