<?php



namespace Symfony\Component\HttpFoundation;


class RequestMatcher implements RequestMatcherInterface
{
    private ?string $path = null;
    private ?string $host = null;
    private ?int $port = null;

    
    private array $methods = [];

    
    private array $ips = [];

    
    private array $attributes = [];

    
    private array $schemes = [];

    
    public function __construct(string $path = null, string $host = null, string|array $methods = null, string|array $ips = null, array $attributes = [], string|array $schemes = null, int $port = null)
    {
        $this->matchPath($path);
        $this->matchHost($host);
        $this->matchMethod($methods);
        $this->matchIps($ips);
        $this->matchScheme($schemes);
        $this->matchPort($port);

        foreach ($attributes as $k => $v) {
            $this->matchAttribute($k, $v);
        }
    }

    
    public function matchScheme(string|array|null $scheme)
    {
        $this->schemes = null !== $scheme ? array_map('strtolower', (array) $scheme) : [];
    }

    
    public function matchHost(?string $regexp)
    {
        $this->host = $regexp;
    }

    
    public function matchPort(?int $port)
    {
        $this->port = $port;
    }

    
    public function matchPath(?string $regexp)
    {
        $this->path = $regexp;
    }

    
    public function matchIp(string $ip)
    {
        $this->matchIps($ip);
    }

    
    public function matchIps(string|array|null $ips)
    {
        $ips = null !== $ips ? (array) $ips : [];

        $this->ips = array_reduce($ips, static function (array $ips, string $ip) {
            return array_merge($ips, preg_split('/\s*,\s*/', $ip));
        }, []);
    }

    
    public function matchMethod(string|array|null $method)
    {
        $this->methods = null !== $method ? array_map('strtoupper', (array) $method) : [];
    }

    
    public function matchAttribute(string $key, string $regexp)
    {
        $this->attributes[$key] = $regexp;
    }

    
    public function matches(Request $request): bool
    {
        if ($this->schemes && !\in_array($request->getScheme(), $this->schemes, true)) {
            return false;
        }

        if ($this->methods && !\in_array($request->getMethod(), $this->methods, true)) {
            return false;
        }

        foreach ($this->attributes as $key => $pattern) {
            $requestAttribute = $request->attributes->get($key);
            if (!\is_string($requestAttribute)) {
                return false;
            }
            if (!preg_match('{'.$pattern.'}', $requestAttribute)) {
                return false;
            }
        }

        if (null !== $this->path && !preg_match('{'.$this->path.'}', rawurldecode($request->getPathInfo()))) {
            return false;
        }

        if (null !== $this->host && !preg_match('{'.$this->host.'}i', $request->getHost())) {
            return false;
        }

        if (null !== $this->port && 0 < $this->port && $request->getPort() !== $this->port) {
            return false;
        }

        if (IpUtils::checkIp($request->getClientIp() ?? '', $this->ips)) {
            return true;
        }

        
        
        return 0 === \count($this->ips);
    }
}
