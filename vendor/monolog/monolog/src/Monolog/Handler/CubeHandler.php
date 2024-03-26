<?php declare(strict_types=1);



namespace Monolog\Handler;

use Monolog\Level;
use Monolog\Utils;
use Monolog\LogRecord;


class CubeHandler extends AbstractProcessingHandler
{
    private ?\Socket $udpConnection = null;
    private ?\CurlHandle $httpConnection = null;
    private string $scheme;
    private string $host;
    private int $port;
    
    private array $acceptedSchemes = ['http', 'udp'];

    
    public function __construct(string $url, int|string|Level $level = Level::Debug, bool $bubble = true)
    {
        $urlInfo = parse_url($url);

        if ($urlInfo === false || !isset($urlInfo['scheme'], $urlInfo['host'], $urlInfo['port'])) {
            throw new \UnexpectedValueException('URL "'.$url.'" is not valid');
        }

        if (!in_array($urlInfo['scheme'], $this->acceptedSchemes, true)) {
            throw new \UnexpectedValueException(
                'Invalid protocol (' . $urlInfo['scheme']  . ').'
                . ' Valid options are ' . implode(', ', $this->acceptedSchemes)
            );
        }

        $this->scheme = $urlInfo['scheme'];
        $this->host = $urlInfo['host'];
        $this->port = $urlInfo['port'];

        parent::__construct($level, $bubble);
    }

    
    protected function connectUdp(): void
    {
        if (!extension_loaded('sockets')) {
            throw new MissingExtensionException('The sockets extension is required to use udp URLs with the CubeHandler');
        }

        $udpConnection = socket_create(AF_INET, SOCK_DGRAM, 0);
        if (false === $udpConnection) {
            throw new \LogicException('Unable to create a socket');
        }

        $this->udpConnection = $udpConnection;
        if (!socket_connect($this->udpConnection, $this->host, $this->port)) {
            throw new \LogicException('Unable to connect to the socket at ' . $this->host . ':' . $this->port);
        }
    }

    
    protected function connectHttp(): void
    {
        if (!extension_loaded('curl')) {
            throw new MissingExtensionException('The curl extension is required to use http URLs with the CubeHandler');
        }

        $httpConnection = curl_init('http:
        if (false === $httpConnection) {
            throw new \LogicException('Unable to connect to ' . $this->host . ':' . $this->port);
        }

        $this->httpConnection = $httpConnection;
        curl_setopt($this->httpConnection, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($this->httpConnection, CURLOPT_RETURNTRANSFER, true);
    }

    
    protected function write(LogRecord $record): void
    {
        $date = $record->datetime;

        $data = ['time' => $date->format('Y-m-d\TH:i:s.uO')];
        $context = $record->context;

        if (isset($context['type'])) {
            $data['type'] = $context['type'];
            unset($context['type']);
        } else {
            $data['type'] = $record->channel;
        }

        $data['data'] = $context;
        $data['data']['level'] = $record->level;

        if ($this->scheme === 'http') {
            $this->writeHttp(Utils::jsonEncode($data));
        } else {
            $this->writeUdp(Utils::jsonEncode($data));
        }
    }

    private function writeUdp(string $data): void
    {
        if (null === $this->udpConnection) {
            $this->connectUdp();
        }

        if (null === $this->udpConnection) {
            throw new \LogicException('No UDP socket could be opened');
        }

        socket_send($this->udpConnection, $data, strlen($data), 0);
    }

    private function writeHttp(string $data): void
    {
        if (null === $this->httpConnection) {
            $this->connectHttp();
        }

        if (null === $this->httpConnection) {
            throw new \LogicException('No connection could be established');
        }

        curl_setopt($this->httpConnection, CURLOPT_POSTFIELDS, '['.$data.']');
        curl_setopt($this->httpConnection, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen('['.$data.']'),
        ]);

        Curl\Util::execute($this->httpConnection, 5, false);
    }
}
