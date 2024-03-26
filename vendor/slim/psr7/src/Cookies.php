<?php



declare(strict_types=1);

namespace Slim\Psr7;

use InvalidArgumentException;

use function array_key_exists;
use function array_replace;
use function count;
use function explode;
use function gmdate;
use function in_array;
use function is_array;
use function is_string;
use function preg_split;
use function rtrim;
use function strtolower;
use function strtotime;
use function urldecode;
use function urlencode;

class Cookies
{
    
    protected array $requestCookies = [];

    
    protected array $responseCookies = [];

    
    protected array $defaults = [
        'value' => '',
        'domain' => null,
        'hostonly' => null,
        'path' => null,
        'expires' => null,
        'secure' => false,
        'httponly' => false,
        'samesite' => null
    ];

    
    public function __construct(array $cookies = [])
    {
        $this->requestCookies = $cookies;
    }

    
    public function setDefaults(array $settings): self
    {
        $this->defaults = array_replace($this->defaults, $settings);

        return $this;
    }

    
    public function get(string $name, $default = null)
    {
        return array_key_exists($name, $this->requestCookies) ? $this->requestCookies[$name] : $default;
    }

    
    public function set(string $name, $value): self
    {
        if (!is_array($value)) {
            $value = ['value' => $value];
        }

        $this->responseCookies[$name] = array_replace($this->defaults, $value);

        return $this;
    }

    
    public function toHeaders(): array
    {
        $headers = [];

        foreach ($this->responseCookies as $name => $properties) {
            $headers[] = $this->toHeader($name, $properties);
        }

        return $headers;
    }

    
    protected function toHeader(string $name, array $properties): string
    {
        $result = urlencode($name) . '=' . urlencode($properties['value']);

        if (isset($properties['domain'])) {
            $result .= '; domain=' . $properties['domain'];
        }

        if (isset($properties['path'])) {
            $result .= '; path=' . $properties['path'];
        }

        if (isset($properties['expires'])) {
            if (is_string($properties['expires'])) {
                $timestamp = strtotime($properties['expires']);
            } else {
                $timestamp = (int) $properties['expires'];
            }
            if ($timestamp && $timestamp !== 0) {
                $result .= '; expires=' . gmdate('D, d-M-Y H:i:s e', $timestamp);
            }
        }

        if (isset($properties['secure']) && $properties['secure']) {
            $result .= '; secure';
        }

        if (isset($properties['hostonly']) && $properties['hostonly']) {
            $result .= '; HostOnly';
        }

        if (isset($properties['httponly']) && $properties['httponly']) {
            $result .= '; HttpOnly';
        }

        if (
            isset($properties['samesite'])
            && in_array(strtolower($properties['samesite']), ['lax', 'strict', 'none'], true)
        ) {
            
            $result .= '; SameSite=' . $properties['samesite'];
        }

        return $result;
    }

    
    public static function parseHeader($header): array
    {
        if (is_array($header)) {
            $header = $header[0] ?? '';
        }

        if (!is_string($header)) {
            throw new InvalidArgumentException('Cannot parse Cookie data. Header value must be a string.');
        }

        $header = rtrim($header, "\r\n");
        $pieces = preg_split('@[;]\s*@', $header);
        $cookies = [];

        if (is_array($pieces)) {
            foreach ($pieces as $cookie) {
                $cookie = explode('=', $cookie, 2);

                if (count($cookie) === 2) {
                    $key = urldecode($cookie[0]);
                    $value = urldecode($cookie[1]);

                    if (!isset($cookies[$key])) {
                        $cookies[$key] = $value;
                    }
                }
            }
        }

        return $cookies;
    }
}
