<?php


namespace Espo\Core\Log;

use Espo\Core\Log\Handler\EspoRotatingFileHandler;
use Espo\Core\Utils\Config;

use Monolog\Handler\HandlerInterface;
use Monolog\Level;
use Monolog\Logger;

class EspoRotatingFileHandlerLoader implements HandlerLoader
{
    public function __construct(
        private readonly Config $config
    ) {}

    public function load(array $params): HandlerInterface
    {
        $filename = $params['filename'] ?? 'data/logs/espo.log';
        $levelCode = $params['level'] ?? Level::Notice->value;
        $level = Logger::toMonologLevel($levelCode);

        return new EspoRotatingFileHandler($this->config, $filename, 0, $level);
    }
}
