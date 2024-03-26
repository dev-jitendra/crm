<?php


namespace Espo\Core\Log;

use Espo\Core\Log\Handler\EspoFileHandler;
use Espo\Core\Log\Handler\EspoRotatingFileHandler;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\Log;

use Monolog\ErrorHandler as MonologErrorHandler;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\HandlerInterface;
use Monolog\Logger;

class LogLoader
{
    private const LINE_FORMAT = "[%datetime%] %level_name%: %message% %context% %extra%\n";
    private const DATE_FORMAT = 'Y-m-d H:i:s';
    private const PATH = 'data/logs/espo.log';

    private const MAX_FILE_NUMBER = 30;
    private const DEFAULT_LEVEL = 'WARNING';

    public function __construct(
        private readonly Config $config,
        private readonly HandlerListLoader $handlerListLoader
    ) {}

    public function load(): Log
    {
        $log = new Log('Espo');

        $handlerDataList = $this->config->get('logger.handlerList') ?? null;

        if ($handlerDataList) {
            $level = $this->config->get('logger.level');

            $handlerList = $this->handlerListLoader->load($handlerDataList, $level);
        }
        else {
            $handlerList = [$this->createDefaultHandler()];
        }

        foreach ($handlerList as $handler) {
            $log->pushHandler($handler);
        }

        $errorHandler = new MonologErrorHandler($log);

        $errorHandler->registerExceptionHandler([], false);
        $errorHandler->registerErrorHandler([], false);

        return $log;
    }

    private function createDefaultHandler(): HandlerInterface
    {
        $path = $this->config->get('logger.path') ?? self::PATH;
        $rotation = $this->config->get('logger.rotation') ?? true;
        $level = $this->config->get('logger.level') ?? self::DEFAULT_LEVEL;

        $levelCode = Logger::toMonologLevel($level);

        if ($rotation) {
            $maxFileNumber = $this->config->get('logger.maxFileNumber') ?? self::MAX_FILE_NUMBER;

            $handler = new EspoRotatingFileHandler($this->config, $path, $maxFileNumber, $levelCode, true);
        }
        else {
            $handler = new EspoFileHandler($this->config, $path, $levelCode, true);
        }

        $formatter = new LineFormatter(
            self::LINE_FORMAT,
            self::DATE_FORMAT,
            false,
            true
        );

        $handler->setFormatter($formatter);

        return $handler;
    }
}
