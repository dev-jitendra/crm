<?php


namespace Espo\Core\Console\Commands;

use Espo\Core\Console\Command;
use Espo\Core\Console\Command\Params;
use Espo\Core\Console\IO;
use Espo\Core\Job\JobManager;
use Espo\Core\Job\Job\Status;
use Espo\Core\Utils\Util;
use Espo\ORM\EntityManager;
use Espo\Entities\Job;

use Throwable;

class RunJob implements Command
{

    public function __construct(private JobManager $jobManager, private EntityManager $entityManager)
    {}

    public function run(Params $params, IO $io): void
    {
        $options = $params->getOptions();
        $argumentList = $params->getArgumentList();

        $jobName = $options['job'] ?? null;
        $targetId = $options['targetId'] ?? null;
        $targetType = $options['targetType'] ?? null;

        if (!$jobName && count($argumentList)) {
            $jobName = $argumentList[0];
        }

        if (!$jobName) {
            $io->writeLine("");
            $io->writeLine("A job name must be specified:");
            $io->writeLine("");

            $io->writeLine(" bin/command run-job [JobName]");
            $io->writeLine("");

            $io->writeLine("To print all available jobs, run:");
            $io->writeLine("");
            $io->writeLine(" bin/command app-info --jobs");
            $io->writeLine("");

            return;
        }

        $jobName = ucfirst(Util::hyphenToCamelCase($jobName));

        $entityManager = $this->entityManager;

        $job = $entityManager->createEntity(Job::ENTITY_TYPE, [
            'name' => $jobName,
            'job' => $jobName,
            'targetType' => $targetType,
            'targetId' => $targetId,
            'attempts' => 0,
            'status' => Status::READY,
        ]);

        try {
            $this->jobManager->runJob($job);
        }
        catch (Throwable $e) {
            $message = "Error: Job '{$jobName}' failed to execute.";

            if ($e->getMessage()) {
                $message .= ' ' . $e->getMessage();
            }

            $io->writeLine($message);
            $io->setExitStatus(1);

            return;
        }

        $io->writeLine("Job '{$jobName}' has been executed.");
    }
}
