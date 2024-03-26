<?php


namespace Espo\Core\ApplicationRunners;

use Espo\Core\Application\Runner;
use Espo\Core\Utils\Preload as PreloadUtil;

use Throwable;


class Preload implements Runner
{
    use Cli;

    public function run(): void
    {
        $preload = new PreloadUtil();

        try {
            $preload->process();
        }
        catch (Throwable $e) {
            $this->processException($e);

            throw $e;
        }

        $count = $preload->getCount();

        echo "Success." . PHP_EOL;
        echo "Files loaded: " . (string) $count . "." . PHP_EOL;
    }

    protected function processException(Throwable $e): void
    {
        echo "Error occurred." . PHP_EOL;

        $msg = $e->getMessage();

        if ($msg) {
            echo "Message: {$msg}" . PHP_EOL;
        }

        $file = $e->getFile();

        if ($file) {
            echo "File: {$file}" . PHP_EOL;
        }

        echo "Line: " . $e->getLine() . PHP_EOL;
    }
}
