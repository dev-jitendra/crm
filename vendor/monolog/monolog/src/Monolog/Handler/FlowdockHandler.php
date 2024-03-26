<?php declare(strict_types=1);



namespace Monolog\Handler;

use Monolog\Level;
use Monolog\Utils;
use Monolog\Formatter\FlowdockFormatter;
use Monolog\Formatter\FormatterInterface;
use Monolog\LogRecord;


class FlowdockHandler extends SocketHandler
{
    protected string $apiToken;

    
    public function __construct(
        string $apiToken,
        $level = Level::Debug,
        bool $bubble = true,
        bool $persistent = false,
        float $timeout = 0.0,
        float $writingTimeout = 10.0,
        ?float $connectionTimeout = null,
        ?int $chunkSize = null
    ) {
        if (!extension_loaded('openssl')) {
            throw new MissingExtensionException('The OpenSSL PHP extension is required to use the FlowdockHandler');
        }

        parent::__construct(
            'ssl:
            $level,
            $bubble,
            $persistent,
            $timeout,
            $writingTimeout,
            $connectionTimeout,
            $chunkSize
        );
        $this->apiToken = $apiToken;
    }

    
    public function setFormatter(FormatterInterface $formatter): HandlerInterface
    {
        if (!$formatter instanceof FlowdockFormatter) {
            throw new \InvalidArgumentException('The FlowdockHandler requires an instance of Monolog\Formatter\FlowdockFormatter to function correctly');
        }

        return parent::setFormatter($formatter);
    }

    
    protected function getDefaultFormatter(): FormatterInterface
    {
        throw new \InvalidArgumentException('The FlowdockHandler must be configured (via setFormatter) with an instance of Monolog\Formatter\FlowdockFormatter to function correctly');
    }

    
    protected function write(LogRecord $record): void
    {
        parent::write($record);

        $this->closeSocket();
    }

    
    protected function generateDataStream(LogRecord $record): string
    {
        $content = $this->buildContent($record);

        return $this->buildHeader($content) . $content;
    }

    
    private function buildContent(LogRecord $record): string
    {
        return Utils::jsonEncode($record->formatted);
    }

    
    private function buildHeader(string $content): string
    {
        $header = "POST /v1/messages/team_inbox/" . $this->apiToken . " HTTP/1.1\r\n";
        $header .= "Host: api.flowdock.com\r\n";
        $header .= "Content-Type: application/json\r\n";
        $header .= "Content-Length: " . strlen($content) . "\r\n";
        $header .= "\r\n";

        return $header;
    }
}
