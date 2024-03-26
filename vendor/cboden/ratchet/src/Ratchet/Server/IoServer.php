<?php
namespace Ratchet\Server;
use Ratchet\MessageComponentInterface;
use React\EventLoop\LoopInterface;
use React\Socket\ServerInterface;
use React\EventLoop\Factory as LoopFactory;
use React\Socket\Server as Reactor;
use React\Socket\SecureServer as SecureReactor;


class IoServer {
    
    public $loop;

    
    public $app;

    
    public $socket;

    
    public function __construct(MessageComponentInterface $app, ServerInterface $socket, LoopInterface $loop = null) {
        if (false === strpos(PHP_VERSION, "hiphop")) {
            gc_enable();
        }

        set_time_limit(0);
        ob_implicit_flush();

        $this->loop = $loop;
        $this->app  = $app;
        $this->socket = $socket;

        $socket->on('connection', array($this, 'handleConnect'));
    }

    
    public static function factory(MessageComponentInterface $component, $port = 80, $address = '0.0.0.0') {
        $loop   = LoopFactory::create();
        $socket = new Reactor($address . ':' . $port, $loop);

        return new static($component, $socket, $loop);
    }

    
    public function run() {
        if (null === $this->loop) {
            throw new \RuntimeException("A React Loop was not provided during instantiation");
        }

        
        $this->loop->run();
        
    }

    
    public function handleConnect($conn) {
        $conn->decor = new IoConnection($conn);
        $conn->decor->resourceId = (int)$conn->stream;

        $uri = $conn->getRemoteAddress();
        $conn->decor->remoteAddress = trim(
            parse_url((strpos($uri, ':
            '[]'
        );

        $this->app->onOpen($conn->decor);

        $conn->on('data', function ($data) use ($conn) {
            $this->handleData($data, $conn);
        });
        $conn->on('close', function () use ($conn) {
            $this->handleEnd($conn);
        });
        $conn->on('error', function (\Exception $e) use ($conn) {
            $this->handleError($e, $conn);
        });
    }

    
    public function handleData($data, $conn) {
        try {
            $this->app->onMessage($conn->decor, $data);
        } catch (\Exception $e) {
            $this->handleError($e, $conn);
        }
    }

    
    public function handleEnd($conn) {
        try {
            $this->app->onClose($conn->decor);
        } catch (\Exception $e) {
            $this->handleError($e, $conn);
        }

        unset($conn->decor);
    }

    
    public function handleError(\Exception $e, $conn) {
        $this->app->onError($conn->decor, $e);
    }
}
