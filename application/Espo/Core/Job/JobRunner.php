<?php


namespace Espo\Core\Job;

use Espo\Core\Exceptions\Error;
use Espo\Core\ORM\EntityManager;
use Espo\Core\ServiceFactory;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\Log;
use Espo\Core\Utils\System;
use Espo\Core\Job\Job\Data;
use Espo\Core\Job\Job\Status;
use Espo\Entities\Job as JobEntity;

use LogicException;
use RuntimeException;
use Throwable;

class JobRunner
{
    public function __construct(
        private JobFactory $jobFactory,
        private ScheduleUtil $scheduleUtil,
        private EntityManager $entityManager,
        private ServiceFactory $serviceFactory,
        private Log $log,
        private Config $config
    ) {}

    
    public function run(JobEntity $jobEntity): void
    {
        try {
            $this->runInternal($jobEntity);
        }
        catch (Throwable $e) {
            throw new LogicException($e->getMessage());
        }
    }

    
    public function runThrowingException(JobEntity $jobEntity): void
    {
        $this->runInternal($jobEntity, true);
    }

    
    public function runById(string $id): void
    {
        if ($id === '') {
            throw new RuntimeException("Empty job ID.");
        }

        
        $jobEntity = $this->entityManager->getEntityById(JobEntity::ENTITY_TYPE, $id);

        if (!$jobEntity) {
            throw new RuntimeException("Job '{$id}' not found.");
        }

        if ($jobEntity->getStatus() !== Status::READY) {
            throw new RuntimeException("Can't run job '{$id}' with not Ready status.");
        }

        $this->setJobRunning($jobEntity);

        $this->run($jobEntity);
    }

    
    private function runInternal(JobEntity $jobEntity, bool $throwException = false): void
    {
        $isSuccess = true;
        $exception = null;

        if ($jobEntity->getStatus() !== Status::RUNNING) {
            $this->setJobRunning($jobEntity);
        }

        try {
            if ($jobEntity->getScheduledJobId()) {
                $this->runScheduledJob($jobEntity);
            }
            else if ($jobEntity->getJob()) {
                $this->runJobNamed($jobEntity);
            }
            else if ($jobEntity->getClassName()) {
                $this->runJobWithClassName($jobEntity);
            }
            else if ($jobEntity->getServiceName()) {
                $this->runService($jobEntity);
            }
            else {
                $id = $jobEntity->getId();

                throw new Error("Not runnable job '{$id}'.");
            }
        }
        catch (Throwable $e) {
            $isSuccess = false;

            $jobId = $jobEntity->hasId() ? $jobEntity->getId() : null;

            $msg = "JobManager: Failed job running, job '{$jobId}'. " .
                $e->getMessage() . "; at " . $e->getFile() . ":" . $e->getLine() . ".";

            if ($this->config->get('logger.printTrace')) {
                $msg .= " :: " . $e->getTraceAsString();
            }

            $this->log->error($msg);

            if ($throwException) {
                $exception = $e;
            }
        }

        $status = $isSuccess ? Status::SUCCESS : Status::FAILED;

        $jobEntity->setStatus($status);

        if ($isSuccess) {
            $jobEntity->setExecutedAtNow();
        }

        $this->entityManager->saveEntity($jobEntity);

        if ($throwException && $exception) {
            throw new $exception($exception->getMessage());
        }

        if ($jobEntity->getScheduledJobId()) {
            $this->scheduleUtil->addLogRecord(
                $jobEntity->getScheduledJobId(),
                $status,
                null,
                $jobEntity->getTargetId(),
                $jobEntity->getTargetType()
            );
        }
    }

    
    private function runJobNamed(JobEntity $jobEntity): void
    {
        $jobName = $jobEntity->getJob();

        if (!$jobName) {
            throw new Error("No job name.");
        }

        $job = $this->jobFactory->create($jobName);

        $this->runJob($job, $jobEntity);
    }

    
    private function runScheduledJob(JobEntity $jobEntity): void
    {
        $jobName = $jobEntity->getScheduledJobJob();

        if (!$jobName) {
            throw new Error("Can't run job '" . $jobEntity->getId() . "'. Not a scheduled job.");
        }

        $job = $this->jobFactory->create($jobName);

        $this->runJob($job, $jobEntity);
    }

    private function runJobWithClassName(JobEntity $jobEntity): void
    {
        $className = $jobEntity->getClassName();

        if (!$className) {
            throw new RuntimeException("No className in job {$jobEntity->getId()}.");
        }

        $job = $this->jobFactory->createByClassName($className);

        $this->runJob($job, $jobEntity);
    }

    
    private function runJob($job, JobEntity $jobEntity): void
    {
        if ($job instanceof JobDataLess) {
            $job->run();

            return;
        }

        $data = Data::create($jobEntity->getData())
            ->withTargetId($jobEntity->getTargetId())
            ->withTargetType($jobEntity->getTargetType());

        $job->run($data);
    }

    
    private function runService(JobEntity $jobEntity): void
    {
        $serviceName = $jobEntity->getServiceName();

        if (!$serviceName) {
            throw new Error("Job with empty serviceName.");
        }

        if (!$this->serviceFactory->checkExists($serviceName)) {
            throw new Error();
        }

        $service = $this->serviceFactory->create($serviceName);

        $methodName = $jobEntity->getMethodName();

        if (!$methodName) {
            throw new Error('Job with empty methodName.');
        }

        if (!method_exists($service, $methodName)) {
            throw new Error("No method '{$methodName}' in service '{$serviceName}'.");
        }

        $service->$methodName(
            $jobEntity->getData(),
            $jobEntity->getTargetId(),
            $jobEntity->getTargetType()
        );
    }

    private function setJobRunning(JobEntity $jobEntity): void
    {
        if (!$jobEntity->getStartedAt()) {
            $jobEntity->setStartedAtNow();
        }

        $jobEntity->setStatus(Status::RUNNING);
        $jobEntity->setPid(System::getPid());

        $this->entityManager->saveEntity($jobEntity);
    }
}
