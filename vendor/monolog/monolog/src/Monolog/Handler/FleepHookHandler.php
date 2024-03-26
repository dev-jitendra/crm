<?php declare(strict_types=1);



namespace Monolog\Handler;

use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\LineFormatter;
use Monolog\Level;
use Monolog\LogRecord;


class FleepHookHandler extends SocketHandler
{
    protected const FLEEP_HOST = 'fleep.io';

    protected const FLEEP_HOOK_URI = '/hook/';

    
    protected string $token;

    
    public function __construct(
        string $token,
        $level = Level::Debug,
        bool $bubble = true,
        bool $persistent = false,
        float $timeout = 0.0,
        float $writingTimeout = 10.0,
        ?float $connectionTimeout = null,
        ?int $chunkSize = null
    ) {
        if (!extension_loaded('openssl')) {
            throw new MissingExtensionException('The OpenSSL PHP extension is required to use the FleepHookHandler');
        }

        $this->token = $token;

        $connectionString = 'ssl:
        parent::__construct(
            $connectionString,
            $level,
            $bubble,
            $persistent,
            $timeout,
            $writingTimeout,
            $connectionTimeout,
            $chunkSize
        );
    }

    
    protected function getDefaultFormatter(): FormatterInterface
    {
        return new LineFormatter(null, null, true, true);
    }

    
    public function write(LogRecord $record): void
    {
        parent::write($record);
        $this->closeSocket();
    }

    
    protected function generateDataStream(LogRecord $record): string
    {
        $content = $this->buildContent($record);

        return $this->buildHeader($content) . $content;
    }

    
    private function buildHeader(string $content): string
    {
        $header = "POST " . static::FLEEP_HOOK_URI . $this->token . " HTTP/1.1\r\n";
        $header .= "Host: " . static::FLEEP_HOST . "\r\n";
        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $header .= "Content-Length: " . strlen($content) . "\r\n";
        $header .= "\r\n";

        return $header;
    }

    
    private function buildContent(LogRecord $record): string
    {
        $dataArray = [
            'message' => $record->formatted,
        ];

        return http_build_query($dataArray);
    }
}
