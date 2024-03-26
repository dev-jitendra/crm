<?php


namespace Espo\Modules\Crm\Jobs;

use Espo\Core\Job\JobDataLess;
use Espo\Core\ORM\EntityManager;
use Espo\Core\Utils\DateTime;
use Espo\Core\Utils\Log;

use Espo\Modules\Crm\Entities\MassEmail;
use Espo\Modules\Crm\Tools\MassEmail\QueueCreator;
use Espo\Modules\Crm\Tools\MassEmail\SendingProcessor;

use Throwable;

class ProcessMassEmail implements JobDataLess
{
    private SendingProcessor $processor;
    private QueueCreator $queue;
    private EntityManager $entityManager;
    private Log $log;

    public function __construct(
        SendingProcessor $processor,
        QueueCreator $queue,
        EntityManager $entityManager,
        Log $log
    ) {
        $this->processor = $processor;
        $this->queue = $queue;
        $this->entityManager = $entityManager;
        $this->log = $log;
    }

    public function run(): void
    {
        $pendingMassEmailList = $this->entityManager
            ->getRDBRepositoryByClass(MassEmail::class)
            ->where([
                'status' => MassEmail::STATUS_PENDING,
                'startAt<=' => date(DateTime::SYSTEM_DATE_TIME_FORMAT),
            ])
            ->find();

        foreach ($pendingMassEmailList as $massEmail) {
            try {
                $this->queue->create($massEmail);
            }
            catch (Throwable $e) {
                $this->log->error(
                    'Job ProcessMassEmail#createQueue ' . $massEmail->getId() . ': [' . $e->getCode() . '] ' .
                    $e->getMessage()
                );
            }
        }

        $massEmailList = $this->entityManager
            ->getRDBRepositoryByClass(MassEmail::class)
            ->where([
                'status' => MassEmail::STATUS_IN_PROCESS,
            ])
            ->find();

        foreach ($massEmailList as $massEmail) {
            try {
                $this->processor->process($massEmail);
            }
            catch (Throwable $e) {
                $this->log->error(
                    'Job ProcessMassEmail#processSending '. $massEmail->getId() . ': [' . $e->getCode() . '] ' .
                    $e->getMessage()
                );
            }
        }
    }
}
