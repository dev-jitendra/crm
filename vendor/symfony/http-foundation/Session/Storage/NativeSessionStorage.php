<?php



namespace Symfony\Component\HttpFoundation\Session\Storage;

use Symfony\Component\HttpFoundation\Session\SessionBagInterface;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\StrictSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\Proxy\AbstractProxy;
use Symfony\Component\HttpFoundation\Session\Storage\Proxy\SessionHandlerProxy;


class_exists(MetadataBag::class);
class_exists(StrictSessionHandler::class);
class_exists(SessionHandlerProxy::class);


class NativeSessionStorage implements SessionStorageInterface
{
    
    protected $bags = [];

    
    protected $started = false;

    
    protected $closed = false;

    
    protected $saveHandler;

    
    protected $metadataBag;

    
    public function __construct(array $options = [], AbstractProxy|\SessionHandlerInterface $handler = null, MetadataBag $metaBag = null)
    {
        if (!\extension_loaded('session')) {
            throw new \LogicException('PHP extension "session" is required.');
        }

        $options += [
            'cache_limiter' => '',
            'cache_expire' => 0,
            'use_cookies' => 1,
            'lazy_write' => 1,
            'use_strict_mode' => 1,
        ];

        session_register_shutdown();

        $this->setMetadataBag($metaBag);
        $this->setOptions($options);
        $this->setSaveHandler($handler);
    }

    
    public function getSaveHandler(): AbstractProxy|\SessionHandlerInterface
    {
        return $this->saveHandler;
    }

    
    public function start(): bool
    {
        if ($this->started) {
            return true;
        }

        if (\PHP_SESSION_ACTIVE === session_status()) {
            throw new \RuntimeException('Failed to start the session: already started by PHP.');
        }

        if (filter_var(\ini_get('session.use_cookies'), \FILTER_VALIDATE_BOOLEAN) && headers_sent($file, $line)) {
            throw new \RuntimeException(sprintf('Failed to start the session because headers have already been sent by "%s" at line %d.', $file, $line));
        }

        $sessionId = $_COOKIE[session_name()] ?? null;
        
        if ($sessionId && $this->saveHandler instanceof AbstractProxy && 'files' === $this->saveHandler->getSaveHandlerName() && !preg_match('/^[a-zA-Z0-9,-]{22,250}$/', $sessionId)) {
            
            session_id(session_create_id());
        }

        
        if (!session_start()) {
            throw new \RuntimeException('Failed to start the session.');
        }

        $this->loadSession();

        return true;
    }

    
    public function getId(): string
    {
        return $this->saveHandler->getId();
    }

    
    public function setId(string $id)
    {
        $this->saveHandler->setId($id);
    }

    
    public function getName(): string
    {
        return $this->saveHandler->getName();
    }

    
    public function setName(string $name)
    {
        $this->saveHandler->setName($name);
    }

    
    public function regenerate(bool $destroy = false, int $lifetime = null): bool
    {
        
        if (\PHP_SESSION_ACTIVE !== session_status()) {
            return false;
        }

        if (headers_sent()) {
            return false;
        }

        if (null !== $lifetime && $lifetime != \ini_get('session.cookie_lifetime')) {
            $this->save();
            ini_set('session.cookie_lifetime', $lifetime);
            $this->start();
        }

        if ($destroy) {
            $this->metadataBag->stampNew();
        }

        return session_regenerate_id($destroy);
    }

    
    public function save()
    {
        
        $session = $_SESSION;

        foreach ($this->bags as $bag) {
            if (empty($_SESSION[$key = $bag->getStorageKey()])) {
                unset($_SESSION[$key]);
            }
        }
        if ($_SESSION && [$key = $this->metadataBag->getStorageKey()] === array_keys($_SESSION)) {
            unset($_SESSION[$key]);
        }

        
        $previousHandler = set_error_handler(function ($type, $msg, $file, $line) use (&$previousHandler) {
            if (\E_WARNING === $type && str_starts_with($msg, 'session_write_close():')) {
                $handler = $this->saveHandler instanceof SessionHandlerProxy ? $this->saveHandler->getHandler() : $this->saveHandler;
                $msg = sprintf('session_write_close(): Failed to write session data with "%s" handler', \get_class($handler));
            }

            return $previousHandler ? $previousHandler($type, $msg, $file, $line) : false;
        });

        try {
            session_write_close();
        } finally {
            restore_error_handler();

            
            if ($_SESSION) {
                $_SESSION = $session;
            }
        }

        $this->closed = true;
        $this->started = false;
    }

    
    public function clear()
    {
        
        foreach ($this->bags as $bag) {
            $bag->clear();
        }

        
        $_SESSION = [];

        
        $this->loadSession();
    }

    
    public function registerBag(SessionBagInterface $bag)
    {
        if ($this->started) {
            throw new \LogicException('Cannot register a bag when the session is already started.');
        }

        $this->bags[$bag->getName()] = $bag;
    }

    
    public function getBag(string $name): SessionBagInterface
    {
        if (!isset($this->bags[$name])) {
            throw new \InvalidArgumentException(sprintf('The SessionBagInterface "%s" is not registered.', $name));
        }

        if (!$this->started && $this->saveHandler->isActive()) {
            $this->loadSession();
        } elseif (!$this->started) {
            $this->start();
        }

        return $this->bags[$name];
    }

    public function setMetadataBag(MetadataBag $metaBag = null)
    {
        if (null === $metaBag) {
            $metaBag = new MetadataBag();
        }

        $this->metadataBag = $metaBag;
    }

    
    public function getMetadataBag(): MetadataBag
    {
        return $this->metadataBag;
    }

    
    public function isStarted(): bool
    {
        return $this->started;
    }

    
    public function setOptions(array $options)
    {
        if (headers_sent() || \PHP_SESSION_ACTIVE === session_status()) {
            return;
        }

        $validOptions = array_flip([
            'cache_expire', 'cache_limiter', 'cookie_domain', 'cookie_httponly',
            'cookie_lifetime', 'cookie_path', 'cookie_secure', 'cookie_samesite',
            'gc_divisor', 'gc_maxlifetime', 'gc_probability',
            'lazy_write', 'name', 'referer_check',
            'serialize_handler', 'use_strict_mode', 'use_cookies',
            'use_only_cookies', 'use_trans_sid',
            'sid_length', 'sid_bits_per_character', 'trans_sid_hosts', 'trans_sid_tags',
        ]);

        foreach ($options as $key => $value) {
            if (isset($validOptions[$key])) {
                if ('cookie_secure' === $key && 'auto' === $value) {
                    continue;
                }
                ini_set('session.'.$key, $value);
            }
        }
    }

    
    public function setSaveHandler(AbstractProxy|\SessionHandlerInterface $saveHandler = null)
    {
        if (!$saveHandler instanceof AbstractProxy &&
            !$saveHandler instanceof \SessionHandlerInterface &&
            null !== $saveHandler) {
            throw new \InvalidArgumentException('Must be instance of AbstractProxy; implement \SessionHandlerInterface; or be null.');
        }

        
        if (!$saveHandler instanceof AbstractProxy && $saveHandler instanceof \SessionHandlerInterface) {
            $saveHandler = new SessionHandlerProxy($saveHandler);
        } elseif (!$saveHandler instanceof AbstractProxy) {
            $saveHandler = new SessionHandlerProxy(new StrictSessionHandler(new \SessionHandler()));
        }
        $this->saveHandler = $saveHandler;

        if (headers_sent() || \PHP_SESSION_ACTIVE === session_status()) {
            return;
        }

        if ($this->saveHandler instanceof SessionHandlerProxy) {
            session_set_save_handler($this->saveHandler, false);
        }
    }

    
    protected function loadSession(array &$session = null)
    {
        if (null === $session) {
            $session = &$_SESSION;
        }

        $bags = array_merge($this->bags, [$this->metadataBag]);

        foreach ($bags as $bag) {
            $key = $bag->getStorageKey();
            $session[$key] = isset($session[$key]) && \is_array($session[$key]) ? $session[$key] : [];
            $bag->initialize($session[$key]);
        }

        $this->started = true;
        $this->closed = false;
    }
}
