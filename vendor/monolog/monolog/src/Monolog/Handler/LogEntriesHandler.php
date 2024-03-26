<?php declare(strict_types=1);



namespace Monolog\Handler;

use Monolog\Level;
use Monolog\LogRecord;


class LogEntriesHandler extends SocketHandler
{
    protected string $logToken;

    
    public function __construct(
        string $token,
        bool $useSSL = true,
        $level = Level::Debug,
        bool $bubble = true,
        string $host = 'data.logentries.com',
        bool $persistent = false,
        float $timeout = 0.0,
        float $writingTimeout = 10.0,
        ?float $connectionTimeout = null,
        ?int $chunkSize = null
    ) {
        if ($useSSL && !extension_loaded('openssl')) {
            throw new MissingExtensionException('The OpenSSL PHP plugin is required to use SSL encrypted connection for LogEntriesHandler');
        }

        $endpoint = $useSSL ? 'ssl:
        parent::__construct(
            $endpoint,
            $level,
            $bubble,
            $persistent,
            $timeout,
            $writingTimeout,
            $connectionTimeout,
            $chunkSize
        );
        $this->logToken = $token;
    }

    
    protected function generateDataStream(LogRecord $record): string
    {
        return $this->logToken . ' ' . $record->formatted;
    }
}
