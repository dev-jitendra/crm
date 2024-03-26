<?php



declare(strict_types=1);

namespace Slim\Psr7;

use InvalidArgumentException;
use Psr\Http\Message\UriInterface;

use function filter_var;
use function is_integer;
use function is_null;
use function is_object;
use function is_string;
use function ltrim;
use function method_exists;
use function preg_replace_callback;
use function rawurlencode;
use function str_replace;
use function strtolower;

use const FILTER_FLAG_IPV6;
use const FILTER_VALIDATE_IP;

class Uri implements UriInterface
{
    public const SUPPORTED_SCHEMES = [
        '' => null,
        'http' => 80,
        'https' => 443
    ];

    
    protected string $scheme = '';

    protected string $user = '';

    protected string $password = '';

    protected string $host = '';

    protected ?int $port;

    protected string $path = '';

    
    protected string $query = '';

    
    protected string $fragment = '';

    
    public function __construct(
        string $scheme,
        string $host,
        ?int $port = null,
        string $path = '/',
        string $query = '',
        string $fragment = '',
        string $user = '',
        string $password = ''
    ) {
        $this->scheme = $this->filterScheme($scheme);
        $this->host = $this->filterHost($host);
        $this->port = $this->filterPort($port);
        $this->path = $this->filterPath($path);
        $this->query = $this->filterQuery($query);
        $this->fragment = $this->filterFragment($fragment);
        $this->user = $this->filterUserInfo($user);
        $this->password = $this->filterUserInfo($password);
    }

    
    public function getScheme(): string
    {
        return $this->scheme;
    }

    
    public function withScheme($scheme)
    {
        $scheme = $this->filterScheme($scheme);
        $clone = clone $this;
        $clone->scheme = $scheme;

        return $clone;
    }

    
    protected function filterScheme($scheme): string
    {
        if (!is_string($scheme)) {
            throw new InvalidArgumentException('Uri scheme must be a string.');
        }

        $scheme = str_replace(':
        if (!key_exists($scheme, static::SUPPORTED_SCHEMES)) {
            throw new InvalidArgumentException(
                'Uri scheme must be one of: "' . implode('", "', array_keys(static::SUPPORTED_SCHEMES)) . '"'
            );
        }

        return $scheme;
    }

    
    public function getAuthority(): string
    {
        $userInfo = $this->getUserInfo();
        $host = $this->getHost();
        $port = $this->getPort();

        return ($userInfo !== '' ? $userInfo . '@' : '') . $host . ($port !== null ? ':' . $port : '');
    }

    
    public function getUserInfo(): string
    {
        $info = $this->user;

        if ($this->password !== '') {
            $info .= ':' . $this->password;
        }

        return $info;
    }

    
    public function withUserInfo($user, $password = null)
    {
        $clone = clone $this;
        $clone->user = $this->filterUserInfo($user);

        if ($clone->user !== '') {
            $clone->password = $this->filterUserInfo($password);
        } else {
            $clone->password = '';
        }

        return $clone;
    }

    
    protected function filterUserInfo(?string $info = null): string
    {
        if (!is_string($info)) {
            return '';
        }

        $match =  preg_replace_callback(
            '/(?:[^a-zA-Z0-9_\-\.~!\$&\'\(\)\*\+,;=]+|%(?![A-Fa-f0-9]{2}))/u',
            function ($match) {
                return rawurlencode($match[0]);
            },
            $info
        );

        return is_string($match) ? $match : '';
    }

    
    public function getHost(): string
    {
        return $this->host;
    }

    
    public function withHost($host)
    {
        $clone = clone $this;
        $clone->host = $this->filterHost($host);

        return $clone;
    }

    
    protected function filterHost($host): string
    {
        if (is_object($host) && method_exists($host, '__toString')) {
            $host = (string) $host;
        }

        if (!is_string($host)) {
            throw new InvalidArgumentException('Uri host must be a string');
        }

        if (filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $host = '[' . $host . ']';
        }

        return strtolower($host);
    }

    
    public function getPort(): ?int
    {
        return $this->port && !$this->hasStandardPort() ? $this->port : null;
    }

    
    public function withPort($port)
    {
        $port = $this->filterPort($port);
        $clone = clone $this;
        $clone->port = $port;

        return $clone;
    }

    
    protected function hasStandardPort(): bool
    {
        return static::SUPPORTED_SCHEMES[$this->scheme] === $this->port;
    }

    
    protected function filterPort($port): ?int
    {
        if (is_null($port) || (is_integer($port) && ($port >= 1 && $port <= 65535))) {
            return $port;
        }

        throw new InvalidArgumentException('Uri port must be null or an integer between 1 and 65535 (inclusive)');
    }

    
    public function getPath(): string
    {
        return $this->path;
    }

    
    public function withPath($path)
    {
        if (!is_string($path)) {
            throw new InvalidArgumentException('Uri path must be a string');
        }

        $clone = clone $this;
        $clone->path = $this->filterPath($path);

        return $clone;
    }

    
    protected function filterPath($path): string
    {
        $match = preg_replace_callback(
            '/(?:[^a-zA-Z0-9_\-\.~:@&=\+\$,\/;%]+|%(?![A-Fa-f0-9]{2}))/',
            function ($match) {
                return rawurlencode($match[0]);
            },
            $path
        );

        return is_string($match) ? $match : '';
    }

    
    public function getQuery(): string
    {
        return $this->query;
    }

    
    public function withQuery($query)
    {
        $query = ltrim($this->filterQuery($query), '?');
        $clone = clone $this;
        $clone->query = $query;

        return $clone;
    }

    
    protected function filterQuery($query): string
    {
        if (is_object($query) && method_exists($query, '__toString')) {
            $query = (string) $query;
        }

        if (!is_string($query)) {
            throw new InvalidArgumentException('Uri query must be a string.');
        }

        $match = preg_replace_callback(
            '/(?:[^a-zA-Z0-9_\-\.~!\$&\'\(\)\*\+,;=%:@\/\?]+|%(?![A-Fa-f0-9]{2}))/',
            function ($match) {
                return rawurlencode($match[0]);
            },
            $query
        );

        return is_string($match) ? $match : '';
    }

    
    public function getFragment(): string
    {
        return $this->fragment;
    }

    
    public function withFragment($fragment)
    {
        $fragment = $this->filterFragment($fragment);
        $clone = clone $this;
        $clone->fragment = $fragment;

        return $clone;
    }

    
    protected function filterFragment($fragment): string
    {
        if (is_object($fragment) && method_exists($fragment, '__toString')) {
            $fragment = (string) $fragment;
        }

        if (!is_string($fragment)) {
            throw new InvalidArgumentException('Uri fragment must be a string.');
        }

        $fragment = ltrim($fragment, '#');

        $match = preg_replace_callback(
            '/(?:[^a-zA-Z0-9_\-\.~!\$&\'\(\)\*\+,;=%:@\/\?]+|%(?![A-Fa-f0-9]{2}))/',
            function ($match) {
                return rawurlencode($match[0]);
            },
            $fragment
        );

        return is_string($match) ? $match : '';
    }

    
    public function __toString(): string
    {
        $scheme = $this->getScheme();
        $authority = $this->getAuthority();
        $path = $this->getPath();
        $query = $this->getQuery();
        $fragment = $this->getFragment();

        if ($path !== '') {
            if ($path[0] !== '/') {
                if ($authority !== '') {
                    
                    $path = '/' . $path;
                }
            } elseif (isset($path[1]) && $path[1] === '/') {
                if ($authority === '') {
                    
                    
                    $path = '/' . ltrim($path, '/');
                }
            }
        }

        return ($scheme !== '' ? $scheme . ':' : '')
            . ($authority !== '' ? '
            . $path
            . ($query !== '' ? '?' . $query : '')
            . ($fragment !== '' ? '#' . $fragment : '');
    }
}
