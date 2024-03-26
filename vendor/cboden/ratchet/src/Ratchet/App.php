<?php
namespace Ratchet;
use React\EventLoop\LoopInterface;
use React\EventLoop\Factory as LoopFactory;
use React\Socket\Server as Reactor;
use React\Socket\SecureServer as SecureReactor;
use Ratchet\Http\HttpServerInterface;
use Ratchet\Http\OriginCheck;
use Ratchet\Wamp\WampServerInterface;
use Ratchet\Server\IoServer;
use Ratchet\Server\FlashPolicy;
use Ratchet\Http\HttpServer;
use Ratchet\Http\Router;
use Ratchet\WebSocket\MessageComponentInterface as WsMessageComponentInterface;
use Ratchet\WebSocket\WsServer;
use Ratchet\Wamp\WampServer;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;


class App {
    
    public $routes;

    
    public $flashServer;

    
    protected $_server;

    
    protected $httpHost;

    
    protected $port;

    
    protected $_routeCounter = 0;

    
    public function __construct($httpHost = 'localhost', $port = 8080, $address = '127.0.0.1', LoopInterface $loop = null, $context = array()) {
        if (extension_loaded('xdebug') && getenv('RATCHET_DISABLE_XDEBUG_WARN') === false) {
            trigger_error('XDebug extension detected. Remember to disable this if performance testing or going live!', E_USER_WARNING);
        }

        if (null === $loop) {
            $loop = LoopFactory::create();
        }

        $this->httpHost = $httpHost;
        $this->port = $port;

        $socket = new Reactor($address . ':' . $port, $loop, $context);

        $this->routes  = new RouteCollection;
        $this->_server = new IoServer(new HttpServer(new Router(new UrlMatcher($this->routes, new RequestContext))), $socket, $loop);

        $policy = new FlashPolicy;
        $policy->addAllowedAccess($httpHost, 80);
        $policy->addAllowedAccess($httpHost, $port);

        if (80 == $port) {
            $flashUri = '0.0.0.0:843';
        } else {
            $flashUri = 8843;
        }
        $flashSock = new Reactor($flashUri, $loop);
        $this->flashServer = new IoServer($policy, $flashSock);
    }

    
    public function route($path, ComponentInterface $controller, array $allowedOrigins = array(), $httpHost = null) {
        if ($controller instanceof HttpServerInterface || $controller instanceof WsServer) {
            $decorated = $controller;
        } elseif ($controller instanceof WampServerInterface) {
            $decorated = new WsServer(new WampServer($controller));
            $decorated->enableKeepAlive($this->_server->loop);
        } elseif ($controller instanceof MessageComponentInterface || $controller instanceof WsMessageComponentInterface) {
            $decorated = new WsServer($controller);
            $decorated->enableKeepAlive($this->_server->loop);
        } else {
            $decorated = $controller;
        }

        if ($httpHost === null) {
            $httpHost = $this->httpHost;
        }

        $allowedOrigins = array_values($allowedOrigins);
        if (0 === count($allowedOrigins)) {
            $allowedOrigins[] = $httpHost;
        }
        if ('*' !== $allowedOrigins[0]) {
            $decorated = new OriginCheck($decorated, $allowedOrigins);
        }

        
        if(empty($this->flashServer) === false) {
            foreach($allowedOrigins as $allowedOrgin) {
                $this->flashServer->app->addAllowedAccess($allowedOrgin, $this->port);
            }
        }

        $this->routes->add('rr-' . ++$this->_routeCounter, new Route($path, array('_controller' => $decorated), array('Origin' => $this->httpHost), array(), $httpHost, array(), array('GET')));

        return $decorated;
    }

    
    public function run() {
        $this->_server->run();
    }
}
