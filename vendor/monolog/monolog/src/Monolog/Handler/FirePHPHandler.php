<?php declare(strict_types=1);



namespace Monolog\Handler;

use Monolog\Formatter\WildfireFormatter;
use Monolog\Formatter\FormatterInterface;
use Monolog\LogRecord;


class FirePHPHandler extends AbstractProcessingHandler
{
    use WebRequestRecognizerTrait;

    
    protected const PROTOCOL_URI = 'http:

    
    protected const STRUCTURE_URI = 'http:

    
    protected const PLUGIN_URI = 'http:

    
    protected const HEADER_PREFIX = 'X-Wf';

    
    protected static bool $initialized = false;

    
    protected static int $messageIndex = 1;

    protected static bool $sendHeaders = true;

    
    protected function createHeader(array $meta, string $message): array
    {
        $header = sprintf('%s-%s', static::HEADER_PREFIX, join('-', $meta));

        return [$header => $message];
    }

    
    protected function createRecordHeader(LogRecord $record): array
    {
        
        
        return $this->createHeader(
            [1, 1, 1, self::$messageIndex++],
            $record->formatted
        );
    }

    
    protected function getDefaultFormatter(): FormatterInterface
    {
        return new WildfireFormatter();
    }

    
    protected function getInitHeaders(): array
    {
        
        return array_merge(
            $this->createHeader(['Protocol', 1], static::PROTOCOL_URI),
            $this->createHeader([1, 'Structure', 1], static::STRUCTURE_URI),
            $this->createHeader([1, 'Plugin', 1], static::PLUGIN_URI)
        );
    }

    
    protected function sendHeader(string $header, string $content): void
    {
        if (!headers_sent() && self::$sendHeaders) {
            header(sprintf('%s: %s', $header, $content));
        }
    }

    
    protected function write(LogRecord $record): void
    {
        if (!self::$sendHeaders || !$this->isWebRequest()) {
            return;
        }

        
        if (!self::$initialized) {
            self::$initialized = true;

            self::$sendHeaders = $this->headersAccepted();
            if (!self::$sendHeaders) {
                return;
            }

            foreach ($this->getInitHeaders() as $header => $content) {
                $this->sendHeader($header, $content);
            }
        }

        $header = $this->createRecordHeader($record);
        if (trim(current($header)) !== '') {
            $this->sendHeader(key($header), current($header));
        }
    }

    
    protected function headersAccepted(): bool
    {
        if (isset($_SERVER['HTTP_USER_AGENT']) && 1 === preg_match('{\bFirePHP/\d+\.\d+\b}', $_SERVER['HTTP_USER_AGENT'])) {
            return true;
        }

        return isset($_SERVER['HTTP_X_FIREPHP_VERSION']);
    }
}
