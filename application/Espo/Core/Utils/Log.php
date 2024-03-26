<?php


namespace Espo\Core\Utils;

use Monolog\Handler\HandlerInterface;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

use DateTimeZone;
use Stringable;

class Log implements LoggerInterface
{
    public const LEVEL_DEBUG = LogLevel::DEBUG;
    public const LEVEL_NOTICE = LogLevel::NOTICE;
    public const LEVEL_WARNING = LogLevel::WARNING;
    public const LEVEL_ERROR = LogLevel::ERROR;

    private Logger $logger;

    
    public function __construct(
        string $name,
        array $handlers = [],
        array $processors = [],
        ?DateTimeZone $timezone = null
    ) {
        $this->logger = new Logger($name, $handlers, $processors, $timezone);
    }

    public function pushHandler(HandlerInterface $handler): self
    {
        $this->logger->pushHandler($handler);

        return $this;
    }

    
    public function emergency(Stringable|string $message, array $context = []): void
    {
        $this->logger->emergency($message, $context);
    }

    
    public function alert(Stringable|string $message, array $context = []): void
    {
        $this->logger->alert($message, $context);
    }

    
    public function critical(Stringable|string $message, array $context = []): void
    {
        $this->logger->critical($message, $context);
    }

    
    public function error(Stringable|string $message, array $context = []): void
    {
        $this->logger->error($message, $context);
    }

    
    public function warning(Stringable|string $message, array $context = []): void
    {
        $this->logger->warning($message, $context);
    }

    
    public function notice(Stringable|string $message, array $context = []): void
    {
        $this->logger->notice($message, $context);
    }

    
    public function info(Stringable|string $message, array $context = []): void
    {
        $this->logger->info($message, $context);
    }

    
    public function debug(Stringable|string $message, array $context = []): void
    {
        $this->logger->debug($message, $context);
    }

    
    public function log($level, Stringable|string $message, array $context = []): void
    {
        $this->logger->log($level, $message, $context);
    }
}
