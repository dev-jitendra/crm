<?php


namespace Espo\Core\Job;

use Espo\Core\Exceptions\Error;
use Espo\Core\Job\QueueProcessor\Params;
use Espo\Core\Utils\File\Manager as FileManager;
use Espo\Core\Utils\Log;
use Espo\Entities\Job as JobEntity;

use RuntimeException;
use Throwable;


class JobManager
{
    private bool $useProcessPool = false;
    protected string $lastRunTimeFile = 'data/cache/application/cronLastRunTime.php';

    public function __construct(
        private FileManager $fileManager,
        private JobRunner $jobRunner,
        private Log $log,
        private ScheduleProcessor $scheduleProcessor,
        private QueueUtil $queueUtil,
        private AsyncPoolFactory $asyncPoolFactory,
        private QueueProcessor $queueProcessor,
        private ConfigDataProvider $configDataProvider
    ) {
        if ($this->configDataProvider->runInParallel()) {
            if ($this->asyncPoolFactory->isSupported()) {
                $this->useProcessPool = true;
            } else {
                $this->log->warning("Enabled `jobRunInParallel` parameter requires pcntl and posix extensions.");
            }
        }
    }

    
    public function process(): void
    {
        if (!$this->checkLastRunTime()) {
            $this->log->info('JobManager: Skip job processing. Too frequent execution.');

            return;
        }

        $this->updateLastRunTime();
        $this->queueUtil->markJobsFailed();
        $this->queueUtil->updateFailedJobAttempts();
        $this->scheduleProcessor->process();
        $this->queueUtil->removePendingJobDuplicates();
        $this->processMainQueue();
    }

    
    public function processQueue(string $queue, int $limit): void
    {
        $params = Params
            ::create()
            ->withQueue($queue)
            ->withLimit($limit)
            ->withUseProcessPool(false)
            ->withNoLock(true);

        $this->queueProcessor->process($params);
    }

    
    public function processGroup(string $group, int $limit): void
    {
        $params = Params
            ::create()
            ->withGroup($group)
            ->withLimit($limit)
            ->withUseProcessPool(false)
            ->withNoLock(true);

        $this->queueProcessor->process($params);
    }

    private function processMainQueue(): void
    {
        $limit = $this->configDataProvider->getMaxPortion();

        $params = Params
            ::create()
            ->withUseProcessPool($this->useProcessPool)
            ->withLimit($limit);

        $this->queueProcessor->process($params);
    }

    
    public function runJobById(string $id): void
    {
        $this->jobRunner->runById($id);
    }

    
    public function runJob(JobEntity $job): void
    {
        $this->jobRunner->runThrowingException($job);
    }

    
    private function getLastRunTime(): int
    {
        if ($this->fileManager->isFile($this->lastRunTimeFile)) {
            try {
                $data = $this->fileManager->getPhpContents($this->lastRunTimeFile);
            }
            catch (RuntimeException) {
                $data = null;
            }

            if (is_array($data) && isset($data['time'])) {
                return (int) $data['time'];
            }
        }

        return time() - $this->configDataProvider->getCronMinInterval() - 1;
    }

    
    private function updateLastRunTime(): void
    {
        $data = ['time' => time()];

        $this->fileManager->putPhpContents($this->lastRunTimeFile, $data, false, true);
    }

    private function checkLastRunTime(): bool
    {
        $currentTime = time();
        $lastRunTime = $this->getLastRunTime();

        $cronMinInterval = $this->configDataProvider->getCronMinInterval();

        if ($currentTime > ($lastRunTime + $cronMinInterval)) {
            return true;
        }

        return false;
    }
}
