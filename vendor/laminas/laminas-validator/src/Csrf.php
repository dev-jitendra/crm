<?php

namespace Laminas\Validator;

use Laminas\Session\Container as SessionContainer;
use Laminas\Stdlib\ArrayUtils;
use Traversable;

use function explode;
use function is_array;
use function is_string;
use function md5;
use function random_bytes;
use function sprintf;
use function str_replace;
use function strtolower;
use function strtr;

class Csrf extends AbstractValidator
{
    
    public const NOT_SAME = 'notSame';

    
    protected $messageTemplates = [
        self::NOT_SAME => 'The form submitted did not originate from the expected site',
    ];

    
    protected $hash;

    
    protected static $hashCache;

    
    protected $name = 'csrf';

    
    protected $salt = 'salt';

    
    protected $session;

    
    protected $timeout = 300;

    
    public function __construct($options = [])
    {
        parent::__construct($options);

        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }

        if (! is_array($options)) {
            $options = (array) $options;
        }

        foreach ($options as $key => $value) {
            switch (strtolower($key)) {
                case 'name':
                    $this->setName($value);
                    break;
                case 'salt':
                    $this->setSalt($value);
                    break;
                case 'session':
                    $this->setSession($value);
                    break;
                case 'timeout':
                    $this->setTimeout($value);
                    break;
                default:
                    
                    break;
            }
        }
    }

    
    public function isValid($value, $context = null)
    {
        if (! is_string($value)) {
            return false;
        }

        $this->setValue($value);

        $tokenId = $this->getTokenIdFromHash($value);
        $hash    = $this->getValidationToken($tokenId);

        $tokenFromValue = $this->getTokenFromHash($value);
        $tokenFromHash  = $this->getTokenFromHash($hash);

        if (! $tokenFromValue || ! $tokenFromHash || ($tokenFromValue !== $tokenFromHash)) {
            $this->error(self::NOT_SAME);
            return false;
        }

        return true;
    }

    
    public function setName($name)
    {
        $this->name = (string) $name;
        return $this;
    }

    
    public function getName()
    {
        return $this->name;
    }

    
    public function setSession(SessionContainer $session)
    {
        $this->session = $session;
        if ($this->hash) {
            $this->initCsrfToken();
        }
        return $this;
    }

    
    public function getSession()
    {
        if (null === $this->session) {
            
            $this->session = new SessionContainer($this->getSessionName());
        }
        return $this->session;
    }

    
    public function setSalt($salt)
    {
        $this->salt = (string) $salt;
        return $this;
    }

    
    public function getSalt()
    {
        return $this->salt;
    }

    
    public function getHash($regenerate = false)
    {
        if ((null === $this->hash) || $regenerate) {
            $this->generateHash();
        }
        return $this->hash;
    }

    
    public function getSessionName()
    {
        return str_replace('\\', '_', self::class) . '_'
            . $this->getSalt() . '_'
            . strtr($this->getName(), ['[' => '_', ']' => '']);
    }

    
    public function setTimeout($ttl)
    {
        $this->timeout = $ttl !== null ? (int) $ttl : null;
        return $this;
    }

    
    public function getTimeout()
    {
        return $this->timeout;
    }

    
    protected function initCsrfToken()
    {
        $session = $this->getSession();
        $timeout = $this->getTimeout();
        if (null !== $timeout) {
            $session->setExpirationSeconds($timeout);
        }

        $hash    = $this->getHash();
        $token   = $this->getTokenFromHash($hash);
        $tokenId = $this->getTokenIdFromHash($hash);

        if (! $session->tokenList) {
            $session->tokenList = [];
        }
        $session->tokenList[$tokenId] = $token;
        $session->hash                = $hash; 
    }

    
    protected function generateHash()
    {
        $token = md5($this->getSalt() . random_bytes(32) . $this->getName());

        $this->hash = $this->formatHash($token, $this->generateTokenId());

        $this->setValue($this->hash);
        $this->initCsrfToken();
    }

    
    protected function generateTokenId()
    {
        return md5(random_bytes(32));
    }

    
    protected function getValidationToken($tokenId = null)
    {
        $session = $this->getSession();

        
        if (! $tokenId && isset($session->hash)) {
            return $session->hash;
        }

        if ($tokenId && isset($session->tokenList[$tokenId])) {
            return $this->formatHash($session->tokenList[$tokenId], $tokenId);
        }

        return null;
    }

    
    protected function formatHash(string $token, string $tokenId)
    {
        return sprintf('%s-%s', $token, $tokenId);
    }

    protected function getTokenFromHash(?string $hash): ?string
    {
        if (null === $hash) {
            return null;
        }

        $data = explode('-', $hash);
        return $data[0] ?: null;
    }

    protected function getTokenIdFromHash(string $hash): ?string
    {
        $data = explode('-', $hash);

        if (! isset($data[1])) {
            return null;
        }

        return $data[1];
    }
}
