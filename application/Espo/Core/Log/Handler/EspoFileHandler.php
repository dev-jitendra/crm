<?php


namespace Espo\Core\Log\Handler;

use Espo\Core\Utils\Config;
use Espo\Core\Utils\File\Manager as FileManager;

use Monolog\Handler\StreamHandler as MonologStreamHandler;
use Monolog\Level;
use Monolog\LogRecord;

use RuntimeException;
use Throwable;

class EspoFileHandler extends MonologStreamHandler
{
    protected FileManager $fileManager;

    protected int $maxErrorMessageLength = 10000;

    public function __construct(
        Config $config,
        string $filename,
        Level $level = Level::Debug,
        bool $bubble = true
    ) {
        parent::__construct($filename, $level, $bubble);

        $defaultPermissions = $config->get('defaultPermissions');

        $this->fileManager = new FileManager($defaultPermissions);
    }

    protected function write(LogRecord $record): void
    {
        if (!$this->url) {
            throw new RuntimeException("Missing a logger file path. Check logger params in config.");
        }

        try {
            if (!is_writable($this->url)) {
                $checkFileResult = $this->fileManager->checkCreateFile($this->url);

                if (!$checkFileResult) {
                    return;
                }
            }

            $this->fileManager->appendContents(
                $this->url,
                $this->pruneMessage($record)
            );
        }
        catch (Throwable $e) {
            $msg = "Could not write file `$this->url`.";

            if ($e->getMessage()) {
                $msg .= " Error message: " . $e->getMessage();
            }

            throw new RuntimeException($msg);
        }
    }

    private function pruneMessage(LogRecord $record): string
    {
        if (strlen($record->message) <= $this->maxErrorMessageLength) {
            return $record->formatted;
        }

        $message = substr($record->message, 0, $this->maxErrorMessageLength) . '...';

        $record = $record->with(message: $message);

        return $this->getFormatter()->format($record);
    }
}
