<?php



namespace Symfony\Component\HttpFoundation;

use Symfony\Component\HttpFoundation\Exception\ConflictingHeadersException;
use Symfony\Component\HttpFoundation\Exception\JsonException;
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class_exists(AcceptHeader::class);
class_exists(FileBag::class);
class_exists(HeaderBag::class);
class_exists(HeaderUtils::class);
class_exists(InputBag::class);
class_exists(ParameterBag::class);
class_exists(ServerBag::class);


class Request
{
    public const HEADER_FORWARDED = 0b000001; 
    public const HEADER_X_FORWARDED_FOR = 0b000010;
    public const HEADER_X_FORWARDED_HOST = 0b000100;
    public const HEADER_X_FORWARDED_PROTO = 0b001000;
    public const HEADER_X_FORWARDED_PORT = 0b010000;
    public const HEADER_X_FORWARDED_PREFIX = 0b100000;

    public const HEADER_X_FORWARDED_AWS_ELB = 0b0011010; 
    public const HEADER_X_FORWARDED_TRAEFIK = 0b0111110; 

    public const METHOD_HEAD = 'HEAD';
    public const METHOD_GET = 'GET';
    public const METHOD_POST = 'POST';
    public const METHOD_PUT = 'PUT';
    public const METHOD_PATCH = 'PATCH';
    public const METHOD_DELETE = 'DELETE';
    public const METHOD_PURGE = 'PURGE';
    public const METHOD_OPTIONS = 'OPTIONS';
    public const METHOD_TRACE = 'TRACE';
    public const METHOD_CONNECT = 'CONNECT';

    
    protected static $trustedProxies = [];

    
    protected static $trustedHostPatterns = [];

    
    protected static $trustedHosts = [];

    protected static $httpMethodParameterOverride = false;

    
    public $attributes;

    
    public $request;

    
    public $query;

    
    public $server;

    
    public $files;

    
    public $cookies;

    
    public $headers;

    
    protected $content;

    
    protected $languages;

    
    protected $charsets;

    
    protected $encodings;

    
    protected $acceptableContentTypes;

    
    protected $pathInfo;

    
    protected $requestUri;

    
    protected $baseUrl;

    
    protected $basePath;

    
    protected $method;

    
    protected $format;

    
    protected $session;

    
    protected $locale;

    
    protected $defaultLocale = 'en';

    
    protected static $formats;

    protected static $requestFactory;

    private ?string $preferredFormat = null;
    private bool $isHostValid = true;
    private bool $isForwardedValid = true;
    private bool $isSafeContentPreferred;

    private static int $trustedHeaderSet = -1;

    private const FORWARDED_PARAMS = [
        self::HEADER_X_FORWARDED_FOR => 'for',
        self::HEADER_X_FORWARDED_HOST => 'host',
        self::HEADER_X_FORWARDED_PROTO => 'proto',
        self::HEADER_X_FORWARDED_PORT => 'host',
    ];

    
    private const TRUSTED_HEADERS = [
        self::HEADER_FORWARDED => 'FORWARDED',
        self::HEADER_X_FORWARDED_FOR => 'X_FORWARDED_FOR',
        self::HEADER_X_FORWARDED_HOST => 'X_FORWARDED_HOST',
        self::HEADER_X_FORWARDED_PROTO => 'X_FORWARDED_PROTO',
        self::HEADER_X_FORWARDED_PORT => 'X_FORWARDED_PORT',
        self::HEADER_X_FORWARDED_PREFIX => 'X_FORWARDED_PREFIX',
    ];

    
    public function __construct(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = null)
    {
        $this->initialize($query, $request, $attributes, $cookies, $files, $server, $content);
    }

    
    public function initialize(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = null)
    {
        $this->request = new InputBag($request);
        $this->query = new InputBag($query);
        $this->attributes = new ParameterBag($attributes);
        $this->cookies = new InputBag($cookies);
        $this->files = new FileBag($files);
        $this->server = new ServerBag($server);
        $this->headers = new HeaderBag($this->server->getHeaders());

        $this->content = $content;
        $this->languages = null;
        $this->charsets = null;
        $this->encodings = null;
        $this->acceptableContentTypes = null;
        $this->pathInfo = null;
        $this->requestUri = null;
        $this->baseUrl = null;
        $this->basePath = null;
        $this->method = null;
        $this->format = null;
    }

    
    public static function createFromGlobals(): static
    {
        $request = self::createRequestFromFactory($_GET, $_POST, [], $_COOKIE, $_FILES, $_SERVER);

        if (str_starts_with($request->headers->get('CONTENT_TYPE', ''), 'application/x-www-form-urlencoded')
            && \in_array(strtoupper($request->server->get('REQUEST_METHOD', 'GET')), ['PUT', 'DELETE', 'PATCH'])
        ) {
            parse_str($request->getContent(), $data);
            $request->request = new InputBag($data);
        }

        return $request;
    }

    
    public static function create(string $uri, string $method = 'GET', array $parameters = [], array $cookies = [], array $files = [], array $server = [], $content = null): static
    {
        $server = array_replace([
            'SERVER_NAME' => 'localhost',
            'SERVER_PORT' => 80,
            'HTTP_HOST' => 'localhost',
            'HTTP_USER_AGENT' => 'Symfony',
            'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*
    public static function setFactory(?callable $callable)
    {
        self::$requestFactory = $callable;
    }

    
    public function duplicate(array $query = null, array $request = null, array $attributes = null, array $cookies = null, array $files = null, array $server = null): static
    {
        $dup = clone $this;
        if (null !== $query) {
            $dup->query = new InputBag($query);
        }
        if (null !== $request) {
            $dup->request = new InputBag($request);
        }
        if (null !== $attributes) {
            $dup->attributes = new ParameterBag($attributes);
        }
        if (null !== $cookies) {
            $dup->cookies = new InputBag($cookies);
        }
        if (null !== $files) {
            $dup->files = new FileBag($files);
        }
        if (null !== $server) {
            $dup->server = new ServerBag($server);
            $dup->headers = new HeaderBag($dup->server->getHeaders());
        }
        $dup->languages = null;
        $dup->charsets = null;
        $dup->encodings = null;
        $dup->acceptableContentTypes = null;
        $dup->pathInfo = null;
        $dup->requestUri = null;
        $dup->baseUrl = null;
        $dup->basePath = null;
        $dup->method = null;
        $dup->format = null;

        if (!$dup->get('_format') && $this->get('_format')) {
            $dup->attributes->set('_format', $this->get('_format'));
        }

        if (!$dup->getRequestFormat(null)) {
            $dup->setRequestFormat($this->getRequestFormat(null));
        }

        return $dup;
    }

    
    public function __clone()
    {
        $this->query = clone $this->query;
        $this->request = clone $this->request;
        $this->attributes = clone $this->attributes;
        $this->cookies = clone $this->cookies;
        $this->files = clone $this->files;
        $this->server = clone $this->server;
        $this->headers = clone $this->headers;
    }

    public function __toString(): string
    {
        $content = $this->getContent();

        $cookieHeader = '';
        $cookies = [];

        foreach ($this->cookies as $k => $v) {
            $cookies[] = \is_array($v) ? http_build_query([$k => $v], '', '; ', \PHP_QUERY_RFC3986) : "$k=$v";
        }

        if ($cookies) {
            $cookieHeader = 'Cookie: '.implode('; ', $cookies)."\r\n";
        }

        return
            sprintf('%s %s %s', $this->getMethod(), $this->getRequestUri(), $this->server->get('SERVER_PROTOCOL'))."\r\n".
            $this->headers.
            $cookieHeader."\r\n".
            $content;
    }

    
    public function overrideGlobals()
    {
        $this->server->set('QUERY_STRING', static::normalizeQueryString(http_build_query($this->query->all(), '', '&')));

        $_GET = $this->query->all();
        $_POST = $this->request->all();
        $_SERVER = $this->server->all();
        $_COOKIE = $this->cookies->all();

        foreach ($this->headers->all() as $key => $value) {
            $key = strtoupper(str_replace('-', '_', $key));
            if (\in_array($key, ['CONTENT_TYPE', 'CONTENT_LENGTH', 'CONTENT_MD5'], true)) {
                $_SERVER[$key] = implode(', ', $value);
            } else {
                $_SERVER['HTTP_'.$key] = implode(', ', $value);
            }
        }

        $request = ['g' => $_GET, 'p' => $_POST, 'c' => $_COOKIE];

        $requestOrder = \ini_get('request_order') ?: \ini_get('variables_order');
        $requestOrder = preg_replace('#[^cgp]#', '', strtolower($requestOrder)) ?: 'gp';

        $_REQUEST = [[]];

        foreach (str_split($requestOrder) as $order) {
            $_REQUEST[] = $request[$order];
        }

        $_REQUEST = array_merge(...$_REQUEST);
    }

    
    public static function setTrustedProxies(array $proxies, int $trustedHeaderSet)
    {
        self::$trustedProxies = array_reduce($proxies, function ($proxies, $proxy) {
            if ('REMOTE_ADDR' !== $proxy) {
                $proxies[] = $proxy;
            } elseif (isset($_SERVER['REMOTE_ADDR'])) {
                $proxies[] = $_SERVER['REMOTE_ADDR'];
            }

            return $proxies;
        }, []);
        self::$trustedHeaderSet = $trustedHeaderSet;
    }

    
    public static function getTrustedProxies(): array
    {
        return self::$trustedProxies;
    }

    
    public static function getTrustedHeaderSet(): int
    {
        return self::$trustedHeaderSet;
    }

    
    public static function setTrustedHosts(array $hostPatterns)
    {
        self::$trustedHostPatterns = array_map(function ($hostPattern) {
            return sprintf('{%s}i', $hostPattern);
        }, $hostPatterns);
        
        self::$trustedHosts = [];
    }

    
    public static function getTrustedHosts(): array
    {
        return self::$trustedHostPatterns;
    }

    
    public static function normalizeQueryString(?string $qs): string
    {
        if ('' === ($qs ?? '')) {
            return '';
        }

        $qs = HeaderUtils::parseQuery($qs);
        ksort($qs);

        return http_build_query($qs, '', '&', \PHP_QUERY_RFC3986);
    }

    
    public static function enableHttpMethodParameterOverride()
    {
        self::$httpMethodParameterOverride = true;
    }

    
    public static function getHttpMethodParameterOverride(): bool
    {
        return self::$httpMethodParameterOverride;
    }

    
    public function get(string $key, mixed $default = null): mixed
    {
        if ($this !== $result = $this->attributes->get($key, $this)) {
            return $result;
        }

        if ($this->query->has($key)) {
            return $this->query->all()[$key];
        }

        if ($this->request->has($key)) {
            return $this->request->all()[$key];
        }

        return $default;
    }

    
    public function getSession(): SessionInterface
    {
        $session = $this->session;
        if (!$session instanceof SessionInterface && null !== $session) {
            $this->setSession($session = $session());
        }

        if (null === $session) {
            throw new SessionNotFoundException('Session has not been set.');
        }

        return $session;
    }

    
    public function hasPreviousSession(): bool
    {
        
        return $this->hasSession() && $this->cookies->has($this->getSession()->getName());
    }

    
    public function hasSession(bool $skipIfUninitialized = false): bool
    {
        return null !== $this->session && (!$skipIfUninitialized || $this->session instanceof SessionInterface);
    }

    public function setSession(SessionInterface $session)
    {
        $this->session = $session;
    }

    
    public function setSessionFactory(callable $factory)
    {
        $this->session = $factory;
    }

    
    public function getClientIps(): array
    {
        $ip = $this->server->get('REMOTE_ADDR');

        if (!$this->isFromTrustedProxy()) {
            return [$ip];
        }

        return $this->getTrustedValues(self::HEADER_X_FORWARDED_FOR, $ip) ?: [$ip];
    }

    
    public function getClientIp(): ?string
    {
        $ipAddresses = $this->getClientIps();

        return $ipAddresses[0];
    }

    
    public function getScriptName(): string
    {
        return $this->server->get('SCRIPT_NAME', $this->server->get('ORIG_SCRIPT_NAME', ''));
    }

    
    public function getPathInfo(): string
    {
        if (null === $this->pathInfo) {
            $this->pathInfo = $this->preparePathInfo();
        }

        return $this->pathInfo;
    }

    
    public function getBasePath(): string
    {
        if (null === $this->basePath) {
            $this->basePath = $this->prepareBasePath();
        }

        return $this->basePath;
    }

    
    public function getBaseUrl(): string
    {
        $trustedPrefix = '';

        
        if ($this->isFromTrustedProxy() && $trustedPrefixValues = $this->getTrustedValues(self::HEADER_X_FORWARDED_PREFIX)) {
            $trustedPrefix = rtrim($trustedPrefixValues[0], '/');
        }

        return $trustedPrefix.$this->getBaseUrlReal();
    }

    
    private function getBaseUrlReal(): string
    {
        if (null === $this->baseUrl) {
            $this->baseUrl = $this->prepareBaseUrl();
        }

        return $this->baseUrl;
    }

    
    public function getScheme(): string
    {
        return $this->isSecure() ? 'https' : 'http';
    }

    
    public function getPort(): int|string|null
    {
        if ($this->isFromTrustedProxy() && $host = $this->getTrustedValues(self::HEADER_X_FORWARDED_PORT)) {
            $host = $host[0];
        } elseif ($this->isFromTrustedProxy() && $host = $this->getTrustedValues(self::HEADER_X_FORWARDED_HOST)) {
            $host = $host[0];
        } elseif (!$host = $this->headers->get('HOST')) {
            return $this->server->get('SERVER_PORT');
        }

        if ('[' === $host[0]) {
            $pos = strpos($host, ':', strrpos($host, ']'));
        } else {
            $pos = strrpos($host, ':');
        }

        if (false !== $pos && $port = substr($host, $pos + 1)) {
            return (int) $port;
        }

        return 'https' === $this->getScheme() ? 443 : 80;
    }

    
    public function getUser(): ?string
    {
        return $this->headers->get('PHP_AUTH_USER');
    }

    
    public function getPassword(): ?string
    {
        return $this->headers->get('PHP_AUTH_PW');
    }

    
    public function getUserInfo(): ?string
    {
        $userinfo = $this->getUser();

        $pass = $this->getPassword();
        if ('' != $pass) {
            $userinfo .= ":$pass";
        }

        return $userinfo;
    }

    
    public function getHttpHost(): string
    {
        $scheme = $this->getScheme();
        $port = $this->getPort();

        if (('http' == $scheme && 80 == $port) || ('https' == $scheme && 443 == $port)) {
            return $this->getHost();
        }

        return $this->getHost().':'.$port;
    }

    
    public function getRequestUri(): string
    {
        if (null === $this->requestUri) {
            $this->requestUri = $this->prepareRequestUri();
        }

        return $this->requestUri;
    }

    
    public function getSchemeAndHttpHost(): string
    {
        return $this->getScheme().':
    }

    
    public function getUri(): string
    {
        if (null !== $qs = $this->getQueryString()) {
            $qs = '?'.$qs;
        }

        return $this->getSchemeAndHttpHost().$this->getBaseUrl().$this->getPathInfo().$qs;
    }

    
    public function getUriForPath(string $path): string
    {
        return $this->getSchemeAndHttpHost().$this->getBaseUrl().$path;
    }

    
    public function getRelativeUriForPath(string $path): string
    {
        
        if (!isset($path[0]) || '/' !== $path[0]) {
            return $path;
        }

        if ($path === $basePath = $this->getPathInfo()) {
            return '';
        }

        $sourceDirs = explode('/', isset($basePath[0]) && '/' === $basePath[0] ? substr($basePath, 1) : $basePath);
        $targetDirs = explode('/', substr($path, 1));
        array_pop($sourceDirs);
        $targetFile = array_pop($targetDirs);

        foreach ($sourceDirs as $i => $dir) {
            if (isset($targetDirs[$i]) && $dir === $targetDirs[$i]) {
                unset($sourceDirs[$i], $targetDirs[$i]);
            } else {
                break;
            }
        }

        $targetDirs[] = $targetFile;
        $path = str_repeat('../', \count($sourceDirs)).implode('/', $targetDirs);

        
        
        
        
        return !isset($path[0]) || '/' === $path[0]
            || false !== ($colonPos = strpos($path, ':')) && ($colonPos < ($slashPos = strpos($path, '/')) || false === $slashPos)
            ? "./$path" : $path;
    }

    
    public function getQueryString(): ?string
    {
        $qs = static::normalizeQueryString($this->server->get('QUERY_STRING'));

        return '' === $qs ? null : $qs;
    }

    
    public function isSecure(): bool
    {
        if ($this->isFromTrustedProxy() && $proto = $this->getTrustedValues(self::HEADER_X_FORWARDED_PROTO)) {
            return \in_array(strtolower($proto[0]), ['https', 'on', 'ssl', '1'], true);
        }

        $https = $this->server->get('HTTPS');

        return !empty($https) && 'off' !== strtolower($https);
    }

    
    public function getHost(): string
    {
        if ($this->isFromTrustedProxy() && $host = $this->getTrustedValues(self::HEADER_X_FORWARDED_HOST)) {
            $host = $host[0];
        } elseif (!$host = $this->headers->get('HOST')) {
            if (!$host = $this->server->get('SERVER_NAME')) {
                $host = $this->server->get('SERVER_ADDR', '');
            }
        }

        
        
        $host = strtolower(preg_replace('/:\d+$/', '', trim($host)));

        
        
        
        if ($host && '' !== preg_replace('/(?:^\[)?[a-zA-Z0-9-:\]_]+\.?/', '', $host)) {
            if (!$this->isHostValid) {
                return '';
            }
            $this->isHostValid = false;

            throw new SuspiciousOperationException(sprintf('Invalid Host "%s".', $host));
        }

        if (\count(self::$trustedHostPatterns) > 0) {
            

            if (\in_array($host, self::$trustedHosts)) {
                return $host;
            }

            foreach (self::$trustedHostPatterns as $pattern) {
                if (preg_match($pattern, $host)) {
                    self::$trustedHosts[] = $host;

                    return $host;
                }
            }

            if (!$this->isHostValid) {
                return '';
            }
            $this->isHostValid = false;

            throw new SuspiciousOperationException(sprintf('Untrusted Host "%s".', $host));
        }

        return $host;
    }

    
    public function setMethod(string $method)
    {
        $this->method = null;
        $this->server->set('REQUEST_METHOD', $method);
    }

    
    public function getMethod(): string
    {
        if (null !== $this->method) {
            return $this->method;
        }

        $this->method = strtoupper($this->server->get('REQUEST_METHOD', 'GET'));

        if ('POST' !== $this->method) {
            return $this->method;
        }

        $method = $this->headers->get('X-HTTP-METHOD-OVERRIDE');

        if (!$method && self::$httpMethodParameterOverride) {
            $method = $this->request->get('_method', $this->query->get('_method', 'POST'));
        }

        if (!\is_string($method)) {
            return $this->method;
        }

        $method = strtoupper($method);

        if (\in_array($method, ['GET', 'HEAD', 'POST', 'PUT', 'DELETE', 'CONNECT', 'OPTIONS', 'PATCH', 'PURGE', 'TRACE'], true)) {
            return $this->method = $method;
        }

        if (!preg_match('/^[A-Z]++$/D', $method)) {
            throw new SuspiciousOperationException(sprintf('Invalid method override "%s".', $method));
        }

        return $this->method = $method;
    }

    
    public function getRealMethod(): string
    {
        return strtoupper($this->server->get('REQUEST_METHOD', 'GET'));
    }

    
    public function getMimeType(string $format): ?string
    {
        if (null === static::$formats) {
            static::initializeFormats();
        }

        return isset(static::$formats[$format]) ? static::$formats[$format][0] : null;
    }

    
    public static function getMimeTypes(string $format): array
    {
        if (null === static::$formats) {
            static::initializeFormats();
        }

        return static::$formats[$format] ?? [];
    }

    
    public function getFormat(?string $mimeType): ?string
    {
        $canonicalMimeType = null;
        if ($mimeType && false !== $pos = strpos($mimeType, ';')) {
            $canonicalMimeType = trim(substr($mimeType, 0, $pos));
        }

        if (null === static::$formats) {
            static::initializeFormats();
        }

        foreach (static::$formats as $format => $mimeTypes) {
            if (\in_array($mimeType, (array) $mimeTypes)) {
                return $format;
            }
            if (null !== $canonicalMimeType && \in_array($canonicalMimeType, (array) $mimeTypes)) {
                return $format;
            }
        }

        return null;
    }

    
    public function setFormat(?string $format, string|array $mimeTypes)
    {
        if (null === static::$formats) {
            static::initializeFormats();
        }

        static::$formats[$format] = \is_array($mimeTypes) ? $mimeTypes : [$mimeTypes];
    }

    
    public function getRequestFormat(?string $default = 'html'): ?string
    {
        if (null === $this->format) {
            $this->format = $this->attributes->get('_format');
        }

        return $this->format ?? $default;
    }

    
    public function setRequestFormat(?string $format)
    {
        $this->format = $format;
    }

    
    public function getContentType(): ?string
    {
        return $this->getFormat($this->headers->get('CONTENT_TYPE', ''));
    }

    
    public function setDefaultLocale(string $locale)
    {
        $this->defaultLocale = $locale;

        if (null === $this->locale) {
            $this->setPhpDefaultLocale($locale);
        }
    }

    
    public function getDefaultLocale(): string
    {
        return $this->defaultLocale;
    }

    
    public function setLocale(string $locale)
    {
        $this->setPhpDefaultLocale($this->locale = $locale);
    }

    
    public function getLocale(): string
    {
        return null === $this->locale ? $this->defaultLocale : $this->locale;
    }

    
    public function isMethod(string $method): bool
    {
        return $this->getMethod() === strtoupper($method);
    }

    
    public function isMethodSafe(): bool
    {
        return \in_array($this->getMethod(), ['GET', 'HEAD', 'OPTIONS', 'TRACE']);
    }

    
    public function isMethodIdempotent(): bool
    {
        return \in_array($this->getMethod(), ['HEAD', 'GET', 'PUT', 'DELETE', 'TRACE', 'OPTIONS', 'PURGE']);
    }

    
    public function isMethodCacheable(): bool
    {
        return \in_array($this->getMethod(), ['GET', 'HEAD']);
    }

    
    public function getProtocolVersion(): ?string
    {
        if ($this->isFromTrustedProxy()) {
            preg_match('~^(HTTP/)?([1-9]\.[0-9]) ~', $this->headers->get('Via') ?? '', $matches);

            if ($matches) {
                return 'HTTP/'.$matches[2];
            }
        }

        return $this->server->get('SERVER_PROTOCOL');
    }

    
    public function getContent(bool $asResource = false)
    {
        $currentContentIsResource = \is_resource($this->content);

        if (true === $asResource) {
            if ($currentContentIsResource) {
                rewind($this->content);

                return $this->content;
            }

            
            if (\is_string($this->content)) {
                $resource = fopen('php:
                fwrite($resource, $this->content);
                rewind($resource);

                return $resource;
            }

            $this->content = false;

            return fopen('php:
        }

        if ($currentContentIsResource) {
            rewind($this->content);

            return stream_get_contents($this->content);
        }

        if (null === $this->content || false === $this->content) {
            $this->content = file_get_contents('php:
        }

        return $this->content;
    }

    
    public function toArray(): array
    {
        if ('' === $content = $this->getContent()) {
            throw new JsonException('Request body is empty.');
        }

        try {
            $content = json_decode($content, true, 512, \JSON_BIGINT_AS_STRING | \JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new JsonException('Could not decode request body.', $e->getCode(), $e);
        }

        if (!\is_array($content)) {
            throw new JsonException(sprintf('JSON content was expected to decode to an array, "%s" returned.', get_debug_type($content)));
        }

        return $content;
    }

    
    public function getETags(): array
    {
        return preg_split('/\s*,\s*/', $this->headers->get('If-None-Match', ''), -1, \PREG_SPLIT_NO_EMPTY);
    }

    public function isNoCache(): bool
    {
        return $this->headers->hasCacheControlDirective('no-cache') || 'no-cache' == $this->headers->get('Pragma');
    }

    
    public function getPreferredFormat(?string $default = 'html'): ?string
    {
        if (null !== $this->preferredFormat || null !== $this->preferredFormat = $this->getRequestFormat(null)) {
            return $this->preferredFormat;
        }

        foreach ($this->getAcceptableContentTypes() as $mimeType) {
            if ($this->preferredFormat = $this->getFormat($mimeType)) {
                return $this->preferredFormat;
            }
        }

        return $default;
    }

    
    public function getPreferredLanguage(array $locales = null): ?string
    {
        $preferredLanguages = $this->getLanguages();

        if (empty($locales)) {
            return $preferredLanguages[0] ?? null;
        }

        if (!$preferredLanguages) {
            return $locales[0];
        }

        $extendedPreferredLanguages = [];
        foreach ($preferredLanguages as $language) {
            $extendedPreferredLanguages[] = $language;
            if (false !== $position = strpos($language, '_')) {
                $superLanguage = substr($language, 0, $position);
                if (!\in_array($superLanguage, $preferredLanguages)) {
                    $extendedPreferredLanguages[] = $superLanguage;
                }
            }
        }

        $preferredLanguages = array_values(array_intersect($extendedPreferredLanguages, $locales));

        return $preferredLanguages[0] ?? $locales[0];
    }

    
    public function getLanguages(): array
    {
        if (null !== $this->languages) {
            return $this->languages;
        }

        $languages = AcceptHeader::fromString($this->headers->get('Accept-Language'))->all();
        $this->languages = [];
        foreach ($languages as $acceptHeaderItem) {
            $lang = $acceptHeaderItem->getValue();
            if (str_contains($lang, '-')) {
                $codes = explode('-', $lang);
                if ('i' === $codes[0]) {
                    
                    
                    
                    if (\count($codes) > 1) {
                        $lang = $codes[1];
                    }
                } else {
                    for ($i = 0, $max = \count($codes); $i < $max; ++$i) {
                        if (0 === $i) {
                            $lang = strtolower($codes[0]);
                        } else {
                            $lang .= '_'.strtoupper($codes[$i]);
                        }
                    }
                }
            }

            $this->languages[] = $lang;
        }

        return $this->languages;
    }

    
    public function getCharsets(): array
    {
        if (null !== $this->charsets) {
            return $this->charsets;
        }

        return $this->charsets = array_map('strval', array_keys(AcceptHeader::fromString($this->headers->get('Accept-Charset'))->all()));
    }

    
    public function getEncodings(): array
    {
        if (null !== $this->encodings) {
            return $this->encodings;
        }

        return $this->encodings = array_map('strval', array_keys(AcceptHeader::fromString($this->headers->get('Accept-Encoding'))->all()));
    }

    
    public function getAcceptableContentTypes(): array
    {
        if (null !== $this->acceptableContentTypes) {
            return $this->acceptableContentTypes;
        }

        return $this->acceptableContentTypes = array_map('strval', array_keys(AcceptHeader::fromString($this->headers->get('Accept'))->all()));
    }

    
    public function isXmlHttpRequest(): bool
    {
        return 'XMLHttpRequest' == $this->headers->get('X-Requested-With');
    }

    
    public function preferSafeContent(): bool
    {
        if (isset($this->isSafeContentPreferred)) {
            return $this->isSafeContentPreferred;
        }

        if (!$this->isSecure()) {
            
            return $this->isSafeContentPreferred = false;
        }

        return $this->isSafeContentPreferred = AcceptHeader::fromString($this->headers->get('Prefer'))->has('safe');
    }

    

    protected function prepareRequestUri()
    {
        $requestUri = '';

        if ('1' == $this->server->get('IIS_WasUrlRewritten') && '' != $this->server->get('UNENCODED_URL')) {
            
            $requestUri = $this->server->get('UNENCODED_URL');
            $this->server->remove('UNENCODED_URL');
            $this->server->remove('IIS_WasUrlRewritten');
        } elseif ($this->server->has('REQUEST_URI')) {
            $requestUri = $this->server->get('REQUEST_URI');

            if ('' !== $requestUri && '/' === $requestUri[0]) {
                
                if (false !== $pos = strpos($requestUri, '#')) {
                    $requestUri = substr($requestUri, 0, $pos);
                }
            } else {
                
                
                $uriComponents = parse_url($requestUri);

                if (isset($uriComponents['path'])) {
                    $requestUri = $uriComponents['path'];
                }

                if (isset($uriComponents['query'])) {
                    $requestUri .= '?'.$uriComponents['query'];
                }
            }
        } elseif ($this->server->has('ORIG_PATH_INFO')) {
            
            $requestUri = $this->server->get('ORIG_PATH_INFO');
            if ('' != $this->server->get('QUERY_STRING')) {
                $requestUri .= '?'.$this->server->get('QUERY_STRING');
            }
            $this->server->remove('ORIG_PATH_INFO');
        }

        
        $this->server->set('REQUEST_URI', $requestUri);

        return $requestUri;
    }

    
    protected function prepareBaseUrl(): string
    {
        $filename = basename($this->server->get('SCRIPT_FILENAME', ''));

        if (basename($this->server->get('SCRIPT_NAME', '')) === $filename) {
            $baseUrl = $this->server->get('SCRIPT_NAME');
        } elseif (basename($this->server->get('PHP_SELF', '')) === $filename) {
            $baseUrl = $this->server->get('PHP_SELF');
        } elseif (basename($this->server->get('ORIG_SCRIPT_NAME', '')) === $filename) {
            $baseUrl = $this->server->get('ORIG_SCRIPT_NAME'); 
        } else {
            
            
            $path = $this->server->get('PHP_SELF', '');
            $file = $this->server->get('SCRIPT_FILENAME', '');
            $segs = explode('/', trim($file, '/'));
            $segs = array_reverse($segs);
            $index = 0;
            $last = \count($segs);
            $baseUrl = '';
            do {
                $seg = $segs[$index];
                $baseUrl = '/'.$seg.$baseUrl;
                ++$index;
            } while ($last > $index && (false !== $pos = strpos($path, $baseUrl)) && 0 != $pos);
        }

        
        $requestUri = $this->getRequestUri();
        if ('' !== $requestUri && '/' !== $requestUri[0]) {
            $requestUri = '/'.$requestUri;
        }

        if ($baseUrl && null !== $prefix = $this->getUrlencodedPrefix($requestUri, $baseUrl)) {
            
            return $prefix;
        }

        if ($baseUrl && null !== $prefix = $this->getUrlencodedPrefix($requestUri, rtrim(\dirname($baseUrl), '/'.\DIRECTORY_SEPARATOR).'/')) {
            
            return rtrim($prefix, '/'.\DIRECTORY_SEPARATOR);
        }

        $truncatedRequestUri = $requestUri;
        if (false !== $pos = strpos($requestUri, '?')) {
            $truncatedRequestUri = substr($requestUri, 0, $pos);
        }

        $basename = basename($baseUrl ?? '');
        if (empty($basename) || !strpos(rawurldecode($truncatedRequestUri), $basename)) {
            
            return '';
        }

        
        
        
        if (\strlen($requestUri) >= \strlen($baseUrl) && (false !== $pos = strpos($requestUri, $baseUrl)) && 0 !== $pos) {
            $baseUrl = substr($requestUri, 0, $pos + \strlen($baseUrl));
        }

        return rtrim($baseUrl, '/'.\DIRECTORY_SEPARATOR);
    }

    
    protected function prepareBasePath(): string
    {
        $baseUrl = $this->getBaseUrl();
        if (empty($baseUrl)) {
            return '';
        }

        $filename = basename($this->server->get('SCRIPT_FILENAME'));
        if (basename($baseUrl) === $filename) {
            $basePath = \dirname($baseUrl);
        } else {
            $basePath = $baseUrl;
        }

        if ('\\' === \DIRECTORY_SEPARATOR) {
            $basePath = str_replace('\\', '/', $basePath);
        }

        return rtrim($basePath, '/');
    }

    
    protected function preparePathInfo(): string
    {
        if (null === ($requestUri = $this->getRequestUri())) {
            return '/';
        }

        
        if (false !== $pos = strpos($requestUri, '?')) {
            $requestUri = substr($requestUri, 0, $pos);
        }
        if ('' !== $requestUri && '/' !== $requestUri[0]) {
            $requestUri = '/'.$requestUri;
        }

        if (null === ($baseUrl = $this->getBaseUrlReal())) {
            return $requestUri;
        }

        $pathInfo = substr($requestUri, \strlen($baseUrl));
        if (false === $pathInfo || '' === $pathInfo) {
            
            return '/';
        }

        return $pathInfo;
    }

    
    protected static function initializeFormats()
    {
        static::$formats = [
            'html' => ['text/html', 'application/xhtml+xml'],
            'txt' => ['text/plain'],
            'js' => ['application/javascript', 'application/x-javascript', 'text/javascript'],
            'css' => ['text/css'],
            'json' => ['application/json', 'application/x-json'],
            'jsonld' => ['application/ld+json'],
            'xml' => ['text/xml', 'application/xml', 'application/x-xml'],
            'rdf' => ['application/rdf+xml'],
            'atom' => ['application/atom+xml'],
            'rss' => ['application/rss+xml'],
            'form' => ['application/x-www-form-urlencoded', 'multipart/form-data'],
        ];
    }

    private function setPhpDefaultLocale(string $locale): void
    {
        
        
        
        try {
            if (class_exists(\Locale::class, false)) {
                \Locale::setDefault($locale);
            }
        } catch (\Exception $e) {
        }
    }

    
    private function getUrlencodedPrefix(string $string, string $prefix): ?string
    {
        if (!str_starts_with(rawurldecode($string), $prefix)) {
            return null;
        }

        $len = \strlen($prefix);

        if (preg_match(sprintf('#^(%%[[:xdigit:]]{2}|.){%d}#', $len), $string, $match)) {
            return $match[0];
        }

        return null;
    }

    private static function createRequestFromFactory(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = null): static
    {
        if (self::$requestFactory) {
            $request = (self::$requestFactory)($query, $request, $attributes, $cookies, $files, $server, $content);

            if (!$request instanceof self) {
                throw new \LogicException('The Request factory must return an instance of Symfony\Component\HttpFoundation\Request.');
            }

            return $request;
        }

        return new static($query, $request, $attributes, $cookies, $files, $server, $content);
    }

    
    public function isFromTrustedProxy(): bool
    {
        return self::$trustedProxies && IpUtils::checkIp($this->server->get('REMOTE_ADDR', ''), self::$trustedProxies);
    }

    private function getTrustedValues(int $type, string $ip = null): array
    {
        $clientValues = [];
        $forwardedValues = [];

        if ((self::$trustedHeaderSet & $type) && $this->headers->has(self::TRUSTED_HEADERS[$type])) {
            foreach (explode(',', $this->headers->get(self::TRUSTED_HEADERS[$type])) as $v) {
                $clientValues[] = (self::HEADER_X_FORWARDED_PORT === $type ? '0.0.0.0:' : '').trim($v);
            }
        }

        if ((self::$trustedHeaderSet & self::HEADER_FORWARDED) && (isset(self::FORWARDED_PARAMS[$type])) && $this->headers->has(self::TRUSTED_HEADERS[self::HEADER_FORWARDED])) {
            $forwarded = $this->headers->get(self::TRUSTED_HEADERS[self::HEADER_FORWARDED]);
            $parts = HeaderUtils::split($forwarded, ',;=');
            $forwardedValues = [];
            $param = self::FORWARDED_PARAMS[$type];
            foreach ($parts as $subParts) {
                if (null === $v = HeaderUtils::combine($subParts)[$param] ?? null) {
                    continue;
                }
                if (self::HEADER_X_FORWARDED_PORT === $type) {
                    if (str_ends_with($v, ']') || false === $v = strrchr($v, ':')) {
                        $v = $this->isSecure() ? ':443' : ':80';
                    }
                    $v = '0.0.0.0'.$v;
                }
                $forwardedValues[] = $v;
            }
        }

        if (null !== $ip) {
            $clientValues = $this->normalizeAndFilterClientIps($clientValues, $ip);
            $forwardedValues = $this->normalizeAndFilterClientIps($forwardedValues, $ip);
        }

        if ($forwardedValues === $clientValues || !$clientValues) {
            return $forwardedValues;
        }

        if (!$forwardedValues) {
            return $clientValues;
        }

        if (!$this->isForwardedValid) {
            return null !== $ip ? ['0.0.0.0', $ip] : [];
        }
        $this->isForwardedValid = false;

        throw new ConflictingHeadersException(sprintf('The request has both a trusted "%s" header and a trusted "%s" header, conflicting with each other. You should either configure your proxy to remove one of them, or configure your project to distrust the offending one.', self::TRUSTED_HEADERS[self::HEADER_FORWARDED], self::TRUSTED_HEADERS[$type]));
    }

    private function normalizeAndFilterClientIps(array $clientIps, string $ip): array
    {
        if (!$clientIps) {
            return [];
        }
        $clientIps[] = $ip; 
        $firstTrustedIp = null;

        foreach ($clientIps as $key => $clientIp) {
            if (strpos($clientIp, '.')) {
                
                
                $i = strpos($clientIp, ':');
                if ($i) {
                    $clientIps[$key] = $clientIp = substr($clientIp, 0, $i);
                }
            } elseif (str_starts_with($clientIp, '[')) {
                
                $i = strpos($clientIp, ']', 1);
                $clientIps[$key] = $clientIp = substr($clientIp, 1, $i - 1);
            }

            if (!filter_var($clientIp, \FILTER_VALIDATE_IP)) {
                unset($clientIps[$key]);

                continue;
            }

            if (IpUtils::checkIp($clientIp, self::$trustedProxies)) {
                unset($clientIps[$key]);

                
                if (null === $firstTrustedIp) {
                    $firstTrustedIp = $clientIp;
                }
            }
        }

        
        return $clientIps ? array_reverse($clientIps) : [$firstTrustedIp];
    }
}
