<?php
namespace Ratchet\Session;
use Ratchet\ConnectionInterface;
use Ratchet\Http\HttpServerInterface;
use Psr\Http\Message\RequestInterface;
use Ratchet\Session\Storage\VirtualSessionStorage;
use Ratchet\Session\Serialize\HandlerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NullSessionHandler;


class SessionProvider implements HttpServerInterface {
    
    protected $_app;

    
    protected $_handler;

    
    protected $_null;

    
    protected $_serializer;

    
    public function __construct(HttpServerInterface $app, \SessionHandlerInterface $handler, array $options = array(), HandlerInterface $serializer = null) {
        $this->_app     = $app;
        $this->_handler = $handler;
        $this->_null    = new NullSessionHandler;

        ini_set('session.auto_start', 0);
        ini_set('session.cache_limiter', '');
        ini_set('session.use_cookies', 0);

        $this->setOptions($options);

        if (null === $serializer) {
            $serialClass = __NAMESPACE__ . "\\Serialize\\{$this->toClassCase(ini_get('session.serialize_handler'))}Handler"; 
            if (!class_exists($serialClass)) {
                throw new \RuntimeException('Unable to parse session serialize handler');
            }

            $serializer = new $serialClass;
        }

        $this->_serializer = $serializer;
    }

    
    public function onOpen(ConnectionInterface $conn, RequestInterface $request = null) {
        $sessionName = ini_get('session.name');

        $id = array_reduce($request->getHeader('Cookie'), function($accumulator, $cookie) use ($sessionName) {
            if ($accumulator) {
                return $accumulator;
            }

            $crumbs = $this->parseCookie($cookie);

            return isset($crumbs['cookies'][$sessionName]) ? $crumbs['cookies'][$sessionName] : false;
        }, false);

        if (null === $request || false === $id) {
            $saveHandler = $this->_null;
            $id = '';
        } else {
            $saveHandler = $this->_handler;
        }

        $conn->Session = new Session(new VirtualSessionStorage($saveHandler, $id, $this->_serializer));

        if (ini_get('session.auto_start')) {
            $conn->Session->start();
        }

        return $this->_app->onOpen($conn, $request);
    }

    
    function onMessage(ConnectionInterface $from, $msg) {
        return $this->_app->onMessage($from, $msg);
    }

    
    function onClose(ConnectionInterface $conn) {
        

        return $this->_app->onClose($conn);
    }

    
    function onError(ConnectionInterface $conn, \Exception $e) {
        return $this->_app->onError($conn, $e);
    }

    
    protected function setOptions(array $options) {
        $all = array(
            'auto_start', 'cache_limiter', 'cookie_domain', 'cookie_httponly',
            'cookie_lifetime', 'cookie_path', 'cookie_secure',
            'entropy_file', 'entropy_length', 'gc_divisor',
            'gc_maxlifetime', 'gc_probability', 'hash_bits_per_character',
            'hash_function', 'name', 'referer_check',
            'serialize_handler', 'use_cookies',
            'use_only_cookies', 'use_trans_sid', 'upload_progress.enabled',
            'upload_progress.cleanup', 'upload_progress.prefix', 'upload_progress.name',
            'upload_progress.freq', 'upload_progress.min-freq', 'url_rewriter.tags'
        );

        foreach ($all as $key) {
            if (!array_key_exists($key, $options)) {
                $options[$key] = ini_get("session.{$key}");
            } else {
                ini_set("session.{$key}", $options[$key]);
            }
        }

        return $options;
    }

    
    protected function toClassCase($langDef) {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $langDef)));
    }

    
    private static $cookieParts = array(
        'domain'      => 'Domain',
        'path'        => 'Path',
        'max_age'     => 'Max-Age',
        'expires'     => 'Expires',
        'version'     => 'Version',
        'secure'      => 'Secure',
        'port'        => 'Port',
        'discard'     => 'Discard',
        'comment'     => 'Comment',
        'comment_url' => 'Comment-Url',
        'http_only'   => 'HttpOnly'
    );

    
    private function parseCookie($cookie, $host = null, $path = null, $decode = false) {
        
        $pieces = array_filter(array_map('trim', explode(';', $cookie)));

        
        if (empty($pieces) || !strpos($pieces[0], '=')) {
            return false;
        }

        
        $data = array_merge(array_fill_keys(array_keys(self::$cookieParts), null), array(
            'cookies'   => array(),
            'data'      => array(),
            'path'      => $path ?: '/',
            'http_only' => false,
            'discard'   => false,
            'domain'    => $host
        ));
        $foundNonCookies = 0;

        
        foreach ($pieces as $part) {

            $cookieParts = explode('=', $part, 2);
            $key = trim($cookieParts[0]);

            if (count($cookieParts) == 1) {
                
                $value = true;
            } else {
                
                $value = trim($cookieParts[1], " \n\r\t\0\x0B\"");
                if ($decode) {
                    $value = urldecode($value);
                }
            }

            
            if (!empty($data['cookies'])) {
                foreach (self::$cookieParts as $mapValue => $search) {
                    if (!strcasecmp($search, $key)) {
                        $data[$mapValue] = $mapValue == 'port' ? array_map('trim', explode(',', $value)) : $value;
                        $foundNonCookies++;
                        continue 2;
                    }
                }
            }

            
            
            $data[$foundNonCookies ? 'data' : 'cookies'][$key] = $value;
        }

        
        if (!$data['expires'] && $data['max_age']) {
            $data['expires'] = time() + (int) $data['max_age'];
        }

        return $data;
    }
}
