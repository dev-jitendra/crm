<?php declare(strict_types=1);



namespace Monolog\Handler;

use Monolog\Level;
use Psr\Log\LoggerInterface;
use Monolog\Formatter\FormatterInterface;
use Monolog\LogRecord;


class PsrHandler extends AbstractHandler implements FormattableHandlerInterface
{
    
    protected LoggerInterface $logger;

    protected FormatterInterface|null $formatter = null;

    
    public function __construct(LoggerInterface $logger, int|string|Level $level = Level::Debug, bool $bubble = true)
    {
        parent::__construct($level, $bubble);

        $this->logger = $logger;
    }

    
    public function handle(LogRecord $record): bool
    {
        if (!$this->isHandling($record)) {
            return false;
        }

        if ($this->formatter !== null) {
            $formatted = $this->formatter->format($record);
            $this->logger->log($record->level->toPsrLogLevel(), (string) $formatted, $record->context);
        } else {
            $this->logger->log($record->level->toPsrLogLevel(), $record->message, $record->context);
        }

        return false === $this->bubble;
    }

    
    public function setFormatter(FormatterInterface $formatter): HandlerInterface
    {
        $this->formatter = $formatter;

        return $this;
    }

    
    public function getFormatter(): FormatterInterface
    {
        if ($this->formatter === null) {
            throw new \LogicException('No formatter has been set and this handler does not have a default formatter');
        }

        return $this->formatter;
    }
}
