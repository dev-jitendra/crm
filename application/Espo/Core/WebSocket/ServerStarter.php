<?php


namespace Espo\Core\WebSocket;

use Espo\Core\Utils\Config;
use Espo\Core\Utils\Metadata;

use React\EventLoop\Factory as EventLoopFactory;
use React\Socket\Server as SocketServer;
use React\Socket\SecureServer as SocketSecureServer;

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\Wamp\WampServer;


class ServerStarter
{
    
    private array $categoriesData;
    private ?string $phpExecutablePath;
    private bool $isDebugMode;
    private bool $useSecureServer;
    private string $port;

    public function __construct(
        private Subscriber $subscriber,
        private Config $config,
        Metadata $metadata
    ) {
        $this->categoriesData = $metadata->get(['app', 'webSocket', 'categories'], []);
        $this->phpExecutablePath = $config->get('phpExecutablePath');
        $this->isDebugMode = (bool) $config->get('webSocketDebugMode');
        $this->useSecureServer = (bool) $config->get('webSocketUseSecureServer');

        $port = $this->config->get('webSocketPort');

        if (!$port) {
            $port = $this->useSecureServer ? '8443' : '8080';
        }

        $this->port = $port;
    }

    
    public function start(): void
    {
        $loop = EventLoopFactory::create();

        $pusher = new Pusher($this->categoriesData, $this->phpExecutablePath, $this->isDebugMode);

        $this->subscriber->subscribe($pusher, $loop);

        $socketServer = new SocketServer('0.0.0.0:' . $this->port, $loop);

        if ($this->useSecureServer) {
            $sslParams = $this->getSslParams();

            $socketServer = new SocketSecureServer($socketServer, $loop, $sslParams);
        }

        new IoServer(
            new HttpServer(
                new WsServer(
                    new WampServer($pusher)
                )
            ),
            $socketServer
        );

        $loop->run();
    }

    
    protected function getSslParams(): array
    {
        $sslParams = [
            'local_cert' => $this->config->get('webSocketSslCertificateFile'),
            'allow_self_signed' => $this->config->get('webSocketSslAllowSelfSigned', false),
            'verify_peer' => false,
        ];

        if ($this->config->get('webSocketSslCertificatePassphrase')) {
            $sslParams['passphrase'] = $this->config->get('webSocketSslCertificatePassphrase');
        }

        if ($this->config->get('webSocketSslCertificateLocalPrivateKey')) {
            $sslParams['local_pk'] = $this->config->get('webSocketSslCertificateLocalPrivateKey');
        }

        return $sslParams;
    }
}
