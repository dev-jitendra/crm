<?php


namespace Espo\Core\ApplicationRunners;

use Espo\Core\Application\Runner;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\Log;

use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;


class Daemon implements Runner
{
    use Cli;

    public function __construct(private Config $config, private Log $log)
    {}

    public function run(): void
    {
        $maxProcessNumber = $this->config->get('daemonMaxProcessNumber');
        $interval = $this->config->get('daemonInterval');
        $timeout = $this->config->get('daemonProcessTimeout');

        $phpExecutablePath = $this->config->get('phpExecutablePath');

        if (!$phpExecutablePath) {
            $phpExecutablePath = (new PhpExecutableFinder)->find();
        }

        if (!$maxProcessNumber || !$interval) {
            $this->log->error("Daemon config params are not set.");

            return;
        }

        $processList = [];

        while (true) { 
            $toSkip = false;
            $runningCount = 0;

            foreach ($processList as $i => $process) {
                if ($process->isRunning()) {
                    $runningCount++;
                } else {
                    unset($processList[$i]);
                }
            }

            $processList = array_values($processList);

            if ($runningCount >= $maxProcessNumber) {
                $toSkip = true;
            }

            if (!$toSkip) {
                $process = new Process([$phpExecutablePath, 'cron.php']);

                $process->setTimeout($timeout);

                $process->start();

                $processList[] = $process;
            }

            sleep($interval);
        }
    }
}
