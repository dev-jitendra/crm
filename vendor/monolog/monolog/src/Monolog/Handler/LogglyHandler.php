<?php declare(strict_types=1);



namespace Monolog\Handler;

use Monolog\Level;
use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\LogglyFormatter;
use function array_key_exists;
use CurlHandle;
use Monolog\LogRecord;


class LogglyHandler extends AbstractProcessingHandler
{
    protected const HOST = 'logs-01.loggly.com';
    protected const ENDPOINT_SINGLE = 'inputs';
    protected const ENDPOINT_BATCH = 'bulk';

    
    protected array $curlHandlers = [];

    protected string $token;

    
    protected array $tag = [];

    
    public function __construct(string $token, int|string|Level $level = Level::Debug, bool $bubble = true)
    {
        if (!extension_loaded('curl')) {
            throw new MissingExtensionException('The curl extension is needed to use the LogglyHandler');
        }

        $this->token = $token;

        parent::__construct($level, $bubble);
    }

    
    protected function getCurlHandler(string $endpoint): CurlHandle
    {
        if (!array_key_exists($endpoint, $this->curlHandlers)) {
            $this->curlHandlers[$endpoint] = $this->loadCurlHandle($endpoint);
        }

        return $this->curlHandlers[$endpoint];
    }

    
    private function loadCurlHandle(string $endpoint): CurlHandle
    {
        $url = sprintf("https:

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        return $ch;
    }

    
    public function setTag(string|array $tag): self
    {
        if ('' === $tag || [] === $tag) {
            $this->tag = [];
        } else {
            $this->tag = is_array($tag) ? $tag : [$tag];
        }

        return $this;
    }

    
    public function addTag(string|array $tag): self
    {
        if ('' !== $tag) {
            $tag = is_array($tag) ? $tag : [$tag];
            $this->tag = array_unique(array_merge($this->tag, $tag));
        }

        return $this;
    }

    protected function write(LogRecord $record): void
    {
        $this->send($record->formatted, static::ENDPOINT_SINGLE);
    }

    public function handleBatch(array $records): void
    {
        $level = $this->level;

        $records = array_filter($records, function ($record) use ($level) {
            return ($record->level->value >= $level->value);
        });

        if (\count($records) > 0) {
            $this->send($this->getFormatter()->formatBatch($records), static::ENDPOINT_BATCH);
        }
    }

    protected function send(string $data, string $endpoint): void
    {
        $ch = $this->getCurlHandler($endpoint);

        $headers = ['Content-Type: application/json'];

        if (\count($this->tag) > 0) {
            $headers[] = 'X-LOGGLY-TAG: '.implode(',', $this->tag);
        }

        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        Curl\Util::execute($ch, 5, false);
    }

    protected function getDefaultFormatter(): FormatterInterface
    {
        return new LogglyFormatter();
    }
}
